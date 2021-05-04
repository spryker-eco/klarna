<?php

/**
 * MIT License
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace SprykerEco\Yves\Klarna\Plugin\SubFormsCreator;

use Generated\Shared\Transfer\KlarnaCheckoutServiceRequestTransfer;
use Generated\Shared\Transfer\KlarnaObjectInitTransfer;
use Generated\Shared\Transfer\PaymentTransfer;
use Generated\Shared\Transfer\QuoteTransfer;
use SprykerEco\Client\Klarna\KlarnaClientInterface;

class DefaultSubFormsCreator extends AbstractSubFormsCreator implements SubFormsCreatorInterface
{

    /**
     * @var \SprykerEco\Client\Klarna\KlarnaClientInterface
     */
    protected $klarnaClient;

    /**
     * @param \SprykerEco\Client\Klarna\KlarnaClientInterface $klarnaClient
     */
    public function __construct(KlarnaClientInterface $klarnaClient)
    {
        $this->klarnaClient = $klarnaClient;
    }

    /**
     * @param \Generated\Shared\Transfer\QuoteTransfer $quoteTransfer
     * @param array $params
     *
     * @return \Spryker\Yves\StepEngine\Dependency\Plugin\Form\SubFormPluginInterface[]
     */
    public function createPaymentMethodsSubForms(QuoteTransfer $quoteTransfer, $params = [])
    {
        $checkoutServiceTransfer = $quoteTransfer->getKlarnaCheckoutService();
        if (!$checkoutServiceTransfer || (isset($params['create']) && $params['create'])) {
            $klarnaObjectInitTransfer = new KlarnaObjectInitTransfer();
            $klarnaObjectInitTransfer->setCurrencyIso3Code($quoteTransfer->getBillingAddress()->getCurrencyIso3Code());
            $klarnaObjectInitTransfer->setIso2Code($quoteTransfer->getBillingAddress()->getIso2Code());
            $klarnaObjectInitTransfer->setClientIp($quoteTransfer->getClientIp());
            $klarnaCheckoutServiceRequestTransfer = new KlarnaCheckoutServiceRequestTransfer();
            $klarnaCheckoutServiceRequestTransfer->setKlarnaObjectInit($klarnaObjectInitTransfer);
            $klarnaCheckoutServiceRequestTransfer->setGrandTotal($quoteTransfer->getTotals()->getGrandTotal());

            $checkoutServiceTransfer = $this->klarnaClient->checkoutService($quoteTransfer);
        }

        $paymentMethodsSubForms = [];
        foreach ($checkoutServiceTransfer->getPaymentMethods() as $paymentMethod) {
            switch ($paymentMethod->getGroupCode()) {
                case 'invoice':
                    $paymentMethodsSubForms[PaymentTransfer::KLARNA_INVOICE] = $this->createKlarnaInvoiceSubFormPlugin($quoteTransfer);
                    break;
                case 'part_payment':
                    $paymentMethodsSubForms[PaymentTransfer::KLARNA_INSTALLMENT] = $this->createKlarnaInstallmentSubFormPlugin($quoteTransfer);
                    break;
            }
        }
        $quoteTransfer->setKlarnaCheckoutService($checkoutServiceTransfer);

        return $paymentMethodsSubForms;
    }

}
