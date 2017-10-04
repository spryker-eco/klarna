<?php

/**
 * MIT License
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace SprykerEco\Yves\Klarna\Handler;

use Generated\Shared\Transfer\PaymentTransfer;
use Generated\Shared\Transfer\QuoteTransfer;
use Spryker\Yves\Messenger\FlashMessenger\FlashMessengerInterface;
use SprykerEco\Client\Klarna\KlarnaClientInterface;
use SprykerEco\Shared\Klarna\KlarnaConstants;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class KlarnaHandler
 *
 * @package SprykerEco\Yves\Klarna\Handler
 *
 * @author Daniel Bohnhardt <daniel.bohnhardt@twt.de>
 */
class KlarnaHandler
{

    /**
     * @var array
     */
    protected static $paymentMethods = [
        PaymentTransfer::KLARNA_INVOICE => 'invoice',
        PaymentTransfer::KLARNA_INSTALLMENT => 'installment',
    ];

    /**
     * @var array
     */
    protected static $klarnaPaymentMethodMapper = [
        PaymentTransfer::KLARNA_INVOICE => KlarnaConstants::BRAND_INVOICE,
        PaymentTransfer::KLARNA_INSTALLMENT => KlarnaConstants::BRAND_INSTALLMENT,
    ];

    /**
     * @var array
     */
    protected static $klarnaGenderMapper = [
        'Mr' => 'Male',
        'Mrs' => 'Female',
    ];

    /**
     * @var \SprykerEco\Client\Klarna\KlarnaClientInterface
     */
    protected $klarnaClient;

    /**
     * @var \Pyz\Yves\Application\Business\Model\FlashMessengerInterface
     *
     * @author Daniel Bohnhardt <daniel.bohnhardt@twt.de>
     */
    protected $flashMessenger;

    /**
     * KlarnaHandler constructor.
     *
     * @author Daniel Bohnhardt <daniel.bohnhardt@twt.de>
     *
     * @param \SprykerEco\Client\Klarna\KlarnaClientInterface $klarnaClient
     * @param \Spryker\Yves\Messenger\FlashMessenger\FlashMessengerInterface $flashMessenger
     */
    public function __construct(KlarnaClientInterface $klarnaClient, FlashMessengerInterface $flashMessenger)
    {
        $this->klarnaClient = $klarnaClient;
        $this->flashMessenger = $flashMessenger;
    }

    /**
     * @param \Symfony\Component\HttpFoundation\Request $request
     * @param \Generated\Shared\Transfer\QuoteTransfer $quoteTransfer
     *
     * @return \Generated\Shared\Transfer\QuoteTransfer
     */
    public function addPaymentToQuote(Request $request, QuoteTransfer $quoteTransfer)
    {
        $paymentSelection = $quoteTransfer->getPayment()->getPaymentSelection();

        $this->setPaymentProviderAndMethod($quoteTransfer, $paymentSelection);
        $this->setKlarnaPayment($request, $quoteTransfer, $paymentSelection);

        return $quoteTransfer;
    }

    /**
     * @author Daniel Bohnhardt <daniel.bohnhardt@twt.de>
     *
     * @param \Generated\Shared\Transfer\QuoteTransfer $quoteTransfer
     *
     * @return \Generated\Shared\Transfer\KlarnaReserveAmountResponseTransfer
     */
    public function updatePayment(QuoteTransfer $quoteTransfer)
    {
        return $this->klarnaClient->updatePayment($quoteTransfer);
    }

    /**
     * @param \Generated\Shared\Transfer\QuoteTransfer $quoteTransfer
     * @param string $paymentSelection
     *
     * @return void
     */
    protected function setPaymentProviderAndMethod(QuoteTransfer $quoteTransfer, $paymentSelection)
    {
        $quoteTransfer->getPayment()
            ->setPaymentProvider(KlarnaConstants::PROVIDER_NAME)
            ->setPaymentMethod(static::$paymentMethods[$paymentSelection]);
    }

    /**
     * @param \Symfony\Component\HttpFoundation\Request $request
     * @param \Generated\Shared\Transfer\QuoteTransfer $quoteTransfer
     * @param string $paymentSelection
     *
     * @return void
     */
    protected function setKlarnaPayment(Request $request, QuoteTransfer $quoteTransfer, $paymentSelection)
    {
        $klarnaPaymentTransfer = $this->getKlarnaPaymentTransfer($quoteTransfer, $paymentSelection);
        $billingAddress = $quoteTransfer->getBillingAddress();
        $currency = $quoteTransfer->getCurrency();

        $klarnaPaymentTransfer
            ->setAccountBrand(self::$klarnaPaymentMethodMapper[$paymentSelection])
            ->setAddress($billingAddress)
            ->setGender(self::$klarnaGenderMapper[$billingAddress->getSalutation()])
            ->setEmail($quoteTransfer->getCustomer()->getEmail())
            ->setCurrencyIso3Code($currency->getCode())
            ->setLanguageIso2Code($billingAddress->getIso2Code())
            ->setClientIp($this->getClientIp());

        if (!$klarnaPaymentTransfer->getDateOfBirth() && $klarnaPaymentTransfer->getInstallmentDateOfBirth()) {
            $klarnaPaymentTransfer->setDateOfBirth($klarnaPaymentTransfer->getInstallmentDateOfBirth());
        }

        $quoteTransfer->getPayment()->setKlarna(clone $klarnaPaymentTransfer);
    }

    /**
     * @author Daniel Bohnhardt <daniel.bohnhardt@twt.de>
     *
     * @param \Generated\Shared\Transfer\QuoteTransfer $quoteTransfer
     *
     * @return void
     */
    public function executePaymentReservation(QuoteTransfer $quoteTransfer)
    {
        $klarnaPaymentTransfer = $quoteTransfer->getPayment()->getKlarna();
        if ($klarnaPaymentTransfer->getPreCheckId()) {
            $return = $this->updatePayment($quoteTransfer);
        } else {
            $return = $this->reservePayment($quoteTransfer);

            if ($return->getReservationNo()) {
                $klarnaPaymentTransfer->setPreCheckId($return->getReservationNo());
            }
        }

        if ($return->getError()) {
            $quoteTransfer->setPayment(null);
            $this->flashMessenger->addErrorMessage($return->getError());
        }
    }

    /**
     * @author Daniel Bohnhardt <daniel.bohnhardt@twt.de>
     *
     * @param \Generated\Shared\Transfer\QuoteTransfer $quoteTransfer
     * @param string $paymentSelection
     *
     * @return \Generated\Shared\Transfer\KlarnaPaymentTransfer
     */
    protected function getKlarnaPaymentTransfer(QuoteTransfer $quoteTransfer, $paymentSelection)
    {
        $method = 'get' . ucfirst($paymentSelection);
        $klarnaPaymentTransfer = $quoteTransfer->getPayment()->$method();

        return $klarnaPaymentTransfer;
    }

    /**
     * @return string|null
     */
    protected function getClientIp()
    {
        $tmpIp = null;

        //Proxy handling.
        if (array_key_exists('REMOTE_ADDR', $_SERVER)) {
            $tmpIp = $_SERVER['REMOTE_ADDR'];
        }

        return $tmpIp;
    }

}
