<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Yves\Klarna\Handler;

use Generated\Shared\Transfer\PaymentTransfer;
use Generated\Shared\Transfer\QuoteTransfer;
use Pyz\Yves\Application\Business\Model\FlashMessengerInterface;
use Spryker\Client\Klarna\KlarnaClientInterface;
use Spryker\Shared\Klarna\KlarnaConstants;
use Spryker\Yves\Klarna\Handler\Exception\KlarnaHandlerException;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class KlarnaHandler
 *
 * @package Spryker\Yves\Klarna\Handler
 *
 * @author Daniel Bohnhardt <daniel.bohnhardt@twt.de>
 */
class KlarnaHandler
{

    const PAYMENT_PROVIDER = 'klarna';

    /**
     * @var array
     */
    protected static $paymentMethods = [
        PaymentTransfer::KLARNA_INVOICE     => 'invoice',
        PaymentTransfer::KLARNA_INSTALLMENT => 'installment',
    ];

    /**
     * @var array
     */
    protected static $klarnaPaymentMethodMapper = [
        PaymentTransfer::KLARNA_INVOICE     => KlarnaConstants::BRAND_INVOICE,
        PaymentTransfer::KLARNA_INSTALLMENT => KlarnaConstants::BRAND_INSTALLMENT,
    ];

    /**
     * @var array
     */
    protected static $klarnaGenderMapper = [
        'Mr'  => 'Male',
        'Mrs' => 'Female',
    ];

    /**
     * @var \Spryker\Client\Klarna\KlarnaClientInterface
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
     * @param \Spryker\Client\Klarna\KlarnaClientInterface $klarnaClient
     * @param \Pyz\Yves\Application\Business\Model\FlashMessengerInterface $flashMessenger
     *
     * @author Daniel Bohnhardt <daniel.bohnhardt@twt.de>
     */
    public function __construct(KlarnaClientInterface $klarnaClient, FlashMessengerInterface $flashMessenger)
    {
        $this->klarnaClient   = $klarnaClient;
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
     * @param \Generated\Shared\Transfer\QuoteTransfer $quoteTransfer
     *
     * @return \Generated\Shared\Transfer\KlarnaReserveAmountResponseTransfer
     * @author Daniel Bohnhardt <daniel.bohnhardt@twt.de>
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
                      ->setPaymentProvider(self::PAYMENT_PROVIDER)
                      ->setPaymentMethod(self::$paymentMethods[$paymentSelection]);
    }

    /**
     * @param \Symfony\Component\HttpFoundation\Request $request
     * @param \Generated\Shared\Transfer\QuoteTransfer $quoteTransfer
     * @param string $paymentSelection
     *
     * @throws \Spryker\Yves\Klarna\Handler\Exception\KlarnaHandlerException
     * @return void
     */
    protected function setKlarnaPayment(Request $request, QuoteTransfer $quoteTransfer, $paymentSelection)
    {
        $klarnaPaymentTransfer = $this->getKlarnaPaymentTransfer($quoteTransfer, $paymentSelection);
        $billingAddress        = $quoteTransfer->getBillingAddress();

        $klarnaPaymentTransfer
            ->setAccountBrand(self::$klarnaPaymentMethodMapper[$paymentSelection])
            ->setAddress($billingAddress)
            ->setGender(self::$klarnaGenderMapper[$billingAddress->getSalutation()])
            ->setEmail($quoteTransfer->getCustomer()->getEmail())
            ->setCurrencyIso3Code($billingAddress->getCurrencyIso3Code())
            ->setLanguageIso2Code($billingAddress->getIso2Code())
            ->setClientIp($quoteTransfer->getClientIp());

        if (!$klarnaPaymentTransfer->getDateOfBirth() && $klarnaPaymentTransfer->getInstallmentDateOfBirth()) {
            $klarnaPaymentTransfer->setDateOfBirth($klarnaPaymentTransfer->getInstallmentDateOfBirth());
        }

        $quoteTransfer->getPayment()->setKlarna(clone $klarnaPaymentTransfer);
    }

    /**
     * @param \Generated\Shared\Transfer\QuoteTransfer $quoteTransfer
     *
     * @return void
     * @author Daniel Bohnhardt <daniel.bohnhardt@twt.de>
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
     * @param \Generated\Shared\Transfer\QuoteTransfer $quoteTransfer
     * @param string $paymentSelection
     *
     * @return \Generated\Shared\Transfer\KlarnaPaymentTransfer
     * @author Daniel Bohnhardt <daniel.bohnhardt@twt.de>
     */
    protected function getKlarnaPaymentTransfer(QuoteTransfer $quoteTransfer, $paymentSelection)
    {
        $method = 'get' . ucfirst($paymentSelection);
        $klarnaPaymentTransfer = $quoteTransfer->getPayment()->$method();

        return $klarnaPaymentTransfer;
    }

}
