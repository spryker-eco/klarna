<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace SprykerEco\Yves\Klarna\Form\DataProvider;

use Generated\Shared\Transfer\KlarnaInstallmentResponseTransfer;
use Generated\Shared\Transfer\KlarnaPaymentTransfer;
use Generated\Shared\Transfer\PaymentTransfer;
use Generated\Shared\Transfer\QuoteTransfer;
use Spryker\Client\Glossary\GlossaryClient;
use SprykerEco\Client\Klarna\KlarnaClientInterface;
use Spryker\Shared\Kernel\Store;
use Spryker\Shared\Library\Currency\CurrencyManager;
use Spryker\Shared\Transfer\AbstractTransfer;
use SprykerEco\Yves\Klarna\Form\InstallmentSubForm;
use Spryker\Yves\StepEngine\Dependency\Form\StepEngineFormDataProviderInterface;

/**
 * Class InstallmentDataProvider
 *
 * @package SprykerEco\Yves\Klarna\Form\DataProvider
 *
 * @author Daniel Bohnhardt <daniel.bohnhardt@twt.de>
 */
class InstallmentDataProvider implements StepEngineFormDataProviderInterface
{

    /**
     * @var \SprykerEco\Client\Klarna\KlarnaClientInterface
     *
     * @author Daniel Bohnhardt <daniel.bohnhardt@twt.de>
     */
    protected $klarnaClient;

    /**
     * @var \Spryker\Client\Glossary\GlossaryClient
     */
    protected $translator;

    /**
     * @var \Spryker\Shared\Kernel\Store
     */
    protected $store;

    /**
     * @var \Generated\Shared\Transfer\KlarnaInstallmentResponseTransfer
     */
    protected $installmentPaymentTransfer;

    /**
     * InstallmentDataProvider constructor.
     *
     * @param \SprykerEco\Client\Klarna\KlarnaClientInterface $klarnaClient
     * @param \Spryker\Client\Glossary\GlossaryClient $translator
     * @param \Spryker\Shared\Kernel\Store $store
     *
     * @author Daniel Bohnhardt <daniel.bohnhardt@twt.de>
     */
    public function __construct(KlarnaClientInterface $klarnaClient, GlossaryClient $translator, Store $store)
    {
        $this->klarnaClient = $klarnaClient;
        $this->translator = $translator;
        $this->store = $store;
    }

    /**
     * @param \Spryker\Shared\Transfer\AbstractTransfer|\Generated\Shared\Transfer\QuoteTransfer $quoteTransfer
     *
     * @return \Generated\Shared\Transfer\QuoteTransfer
     * @author Daniel Bohnhardt <daniel.bohnhardt@twt.de>
     */
    public function getData(AbstractTransfer $quoteTransfer)
    {
        if (empty($quoteTransfer->getPayment())) {
            $paymentTransfer = new PaymentTransfer();
            $paymentTransfer->setKlarna(new KlarnaPaymentTransfer());
            $paymentTransfer->setKlarnaInstallment(new KlarnaPaymentTransfer());
            $quoteTransfer->setPayment($paymentTransfer);
        }

        return $quoteTransfer;
    }

    /**
     * @param \Spryker\Shared\Transfer\AbstractTransfer|\Generated\Shared\Transfer\QuoteTransfer $quoteTransfer
     *
     * @return array
     * @author Daniel Bohnhardt <daniel.bohnhardt@twt.de>
     */
    public function getOptions(AbstractTransfer $quoteTransfer)
    {
        return [
            InstallmentSubForm::PAYMENT_CHOICES => $this->getPaymentChoices($quoteTransfer),
        ];
    }

    /**
     * @param \Generated\Shared\Transfer\QuoteTransfer $quoteTransfer
     * @param bool $canTakeFromSession
     *
     * @return array
     * @author Daniel Bohnhardt <daniel.bohnhardt@twt.de>
     */
    public function getInstallmentPaymentTransfer(QuoteTransfer $quoteTransfer, $canTakeFromSession = true)
    {
        if (!$canTakeFromSession || !$this->klarnaClient->hasInstallmentsInSession()) {
            $paymentTransfer = ($quoteTransfer->getPayment() instanceof PaymentTransfer)?
                  $quoteTransfer->getPayment()
                : new PaymentTransfer();
            $klarnaPaymentTransfer = new KlarnaPaymentTransfer();
            $billingAddress        = $quoteTransfer->getBillingAddress();

            $klarnaPaymentTransfer
                ->setCurrencyIso3Code($billingAddress->getCurrencyIso3Code())
                ->setLanguageIso2Code($billingAddress->getIso2Code());
            $paymentTransfer->setKlarna($klarnaPaymentTransfer);
            $quoteTransfer->setPayment($paymentTransfer);
            $this->installmentPaymentTransfer = $this->klarnaClient->getInstallments($quoteTransfer);
            $this->klarnaClient->storeInstallmentsInSession($this->installmentPaymentTransfer);
        } else {
            $this->installmentPaymentTransfer = $this->klarnaClient->getInstallmentsFromSession();
        }

        return $this->installmentPaymentTransfer;
    }

    /**
     * @param \Generated\Shared\Transfer\QuoteTransfer $quoteTransfer
     *
     * @return array
     * @author Daniel Bohnhardt <daniel.bohnhardt@twt.de>
     */
    private function getPaymentChoices(QuoteTransfer $quoteTransfer)
    {
        $installmentPaymentTransfer = $this->getInstallmentPaymentTransfer($quoteTransfer, false);

        return $this->buildChoices($installmentPaymentTransfer);
    }

    /**
     * @return string
     */
    public function getCurrency()
    {
        return CurrencyManager::getInstance()->getDefaultCurrency()->getIsoCode();
    }

    /**
     * @param \Generated\Shared\Transfer\KlarnaInstallmentResponseTransfer $installmentPaymentTransfer
     *
     * @return array
     * @author Daniel Bohnhardt <daniel.bohnhardt@twt.de>
     */
    protected function buildChoices(KlarnaInstallmentResponseTransfer $installmentPaymentTransfer)
    {
        $choices = [];
        foreach ($installmentPaymentTransfer->getPayments() as $paymentDetail) {
            $choices[$paymentDetail->getId()] = $this->buildChoice($paymentDetail);
        }

        return $choices;
    }

    /**
     * @return \Spryker\Client\Glossary\GlossaryClient
     */
    protected function getTranslator()
    {
        return $this->translator;
    }

    /**
     * @return \Spryker\Shared\Kernel\Store
     */
    public function getStore()
    {
        return $this->store;
    }

    /**
     * @param \Generated\Shared\Transfer\KlarnaPClassTransfer $paymentDetail
     *
     * @return string
     * @author Daniel Bohnhardt <daniel.bohnhardt@twt.de>
     */
    protected function buildChoice($paymentDetail)
    {
        $translator = $this->getTranslator();
        $locale = $this->getStore()->getCurrentLocale();

        $labelParts = [
            'payment.method.checkout.klarna.duration' =>
                $paymentDetail->getMonth() . ' ' . $translator->translate('payment.method.checkout.klarna.months', $locale),
            'payment.method.checkout.klarna.invoice_fee' => $paymentDetail->getInvoiceFee(),
            'payment.method.checkout.klarna.start_fee' => $paymentDetail->getStartFee(),
            'payment.method.checkout.klarna.monthly_cost' => $paymentDetail->getMonthlyCosts(),
            'payment.method.checkout.klarna.interest_rate' => $paymentDetail->getInterestRate(),
            'payment.method.checkout.klarna.min_amount' => $paymentDetail->getMinAmount(),
        ];

        array_walk(
            $labelParts,
            function (&$value, $key) use ($translator, $locale) {
                $value = $translator->translate($key, $locale) . ': ' . $value;
            }
        );

        return implode(', ', $labelParts);
    }

}
