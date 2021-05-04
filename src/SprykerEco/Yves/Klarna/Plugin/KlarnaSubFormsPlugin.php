<?php

/**
 * MIT License
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace SprykerEco\Yves\Klarna\Plugin;

use Generated\Shared\Transfer\QuoteTransfer;
use Spryker\Yves\Kernel\AbstractPlugin;

/**
 * @package SprykerEco\Yves\Klarna\Plugin
 * @method \SprykerEco\Yves\Klarna\KlarnaFactory getFactory()
 */
class KlarnaSubFormsPlugin extends AbstractPlugin
{

    /**
     * @var \SprykerEco\Yves\Klarna\Plugin\PluginCountryFactory
     */
    protected $pluginCountryFactory;

    public function __construct()
    {
        $this->pluginCountryFactory = new PluginCountryFactory();
    }

    /**
     * @param \Generated\Shared\Transfer\QuoteTransfer $quoteTransfer
     * @param bool $create
     *
     * @return \Spryker\Yves\StepEngine\Dependency\Plugin\Form\SubFormPluginInterface[]
     */
    public function getPaymentMethodsSubForms(QuoteTransfer $quoteTransfer, $create = true)
    {
        // Klarna does not work with companies
        if (!$quoteTransfer->getBillingAddress()) {
            return [];
        }
        if ($quoteTransfer->getBillingAddress()->getIso2Code() === null) {
            return [];
        }
        if (!$quoteTransfer->getBillingSameAsShipping() &&
            (
                $quoteTransfer->getBillingAddress()->getFirstName() !== $quoteTransfer->getShippingAddress()->getFirstName()
                || $quoteTransfer->getBillingAddress()->getLastName() !== $quoteTransfer->getShippingAddress()->getLastName()
//                || $quoteTransfer->getBillingAddress()->getFkCountry() !== $quoteTransfer->getShippingAddress()->getFkCountry()
            )
        ) {
            return [];
        }
        $subFormsCreator = $this->pluginCountryFactory
            ->createSubFormsCreator($quoteTransfer->getBillingAddress()->getIso2Code());

        $paymentMethodsSubForms = $subFormsCreator->createPaymentMethodsSubForms($quoteTransfer, ['create' => $create]);

        return $paymentMethodsSubForms;
    }

}
