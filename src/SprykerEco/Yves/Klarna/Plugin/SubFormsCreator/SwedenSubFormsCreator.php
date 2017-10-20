<?php

/**
 * MIT License
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace SprykerEco\Yves\Klarna\Plugin\SubFormsCreator;

use Generated\Shared\Transfer\PaymentTransfer;
use Generated\Shared\Transfer\QuoteTransfer;

class SwedenSubFormsCreator extends AbstractSubFormsCreator implements SubFormsCreatorInterface
{
    /**
     * @param \Generated\Shared\Transfer\QuoteTransfer $quoteTransfer
     * @param array $params
     *
     * @return \Spryker\Yves\StepEngine\Dependency\Plugin\Form\SubFormPluginInterface[]
     */
    public function createPaymentMethodsSubForms(QuoteTransfer $quoteTransfer, $params = [])
    {
        // Order as a company with invoice is allowed in SE, NO, DK, FI
        $paymentMethods = [
            PaymentTransfer::KLARNA_INVOICE => $this->createKlarnaInvoiceSubFormPlugin($quoteTransfer),
        ];
        if (trim($quoteTransfer->getBillingAddress()->getCompany()) == '') {
            $paymentMethods[PaymentTransfer::KLARNA_INSTALLMENT] = $this->createKlarnaInstallmentSubFormPlugin($quoteTransfer);
        }

        return $paymentMethods;
    }
}
