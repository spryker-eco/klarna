<?php

/**
 * MIT License
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace SprykerEco\Yves\Klarna\Plugin\SubFormsCreator;

use Generated\Shared\Transfer\PaymentTransfer;
use Generated\Shared\Transfer\QuoteTransfer;

class AustriaSubFormsCreator extends AbstractSubFormsCreator implements SubFormsCreatorInterface
{

    /**
     * @param \Generated\Shared\Transfer\QuoteTransfer $quoteTransfer
     * @param array $params
     *
     * @return \Spryker\Yves\StepEngine\Dependency\Plugin\Form\SubFormPluginInterface[]
     */
    public function createPaymentMethodsSubForms(QuoteTransfer $quoteTransfer, $params = [])
    {
        // Klarna does not work with companies
        if (trim($quoteTransfer->getBillingAddress()->getCompany()) != '') {
            return [];
        }
        return [
            PaymentTransfer::KLARNA_INVOICE => $this->createKlarnaInvoiceSubFormPlugin($quoteTransfer),
        ];
    }

}
