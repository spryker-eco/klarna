<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace SprykerEco\Yves\Klarna\Dependency\Injector;

use Generated\Shared\Transfer\QuoteTransfer;
use Spryker\Shared\Kernel\ContainerInterface;
use Spryker\Shared\Kernel\Dependency\Injector\DependencyInjectorInterface;
use Spryker\Yves\Checkout\CheckoutDependencyProvider;
use Spryker\Yves\StepEngine\Dependency\Plugin\Form\SubFormPluginCollection;
use Spryker\Yves\StepEngine\Dependency\Plugin\Handler\StepHandlerPluginCollection;
use SprykerEco\Shared\Klarna\KlarnaConstants;
use SprykerEco\Yves\Klarna\Plugin\KlarnaHandlerPlugin;
use SprykerEco\Yves\Klarna\Plugin\PluginCountryFactory;

class CheckoutDependencyInjector implements DependencyInjectorInterface
{

    /**
     * @param \Spryker\Shared\Kernel\ContainerInterface|\Spryker\Yves\Kernel\Container $container
     *
     * @return \Spryker\Shared\Kernel\ContainerInterface|\Spryker\Yves\Kernel\Container
     */
    public function inject(ContainerInterface $container)
    {
        $container = $this->injectPaymentSubForms($container);
        $container = $this->injectPaymentMethodHandler($container);

        return $container;
    }

    /**
     * @param \Spryker\Shared\Kernel\ContainerInterface $container
     *
     * @return \Spryker\Shared\Kernel\ContainerInterface
     */
    protected function injectPaymentSubForms(ContainerInterface $container)
    {
        $quoteTransfer = $container
            ->getLocator()
            ->cart()
            ->client()
            ->getQuote();

        $paymentMethodsSubForms = $this->getPaymentMethodsSubForms($quoteTransfer);
        $container->extend(CheckoutDependencyProvider::PAYMENT_SUB_FORMS, function (SubFormPluginCollection $paymentSubForms) use ($paymentMethodsSubForms) {
            foreach ($paymentMethodsSubForms as $paymentMethodsSubForm) {
                $paymentSubForms->add($paymentMethodsSubForm);
            }
            return $paymentSubForms;
        });

        return $container;
    }

    /**
     * @param \Spryker\Shared\Kernel\ContainerInterface $container
     *
     * @return \Spryker\Shared\Kernel\ContainerInterface
     */
    protected function injectPaymentMethodHandler(ContainerInterface $container)
    {
        $container->extend(CheckoutDependencyProvider::PAYMENT_METHOD_HANDLER, function (StepHandlerPluginCollection $paymentMethodHandler) {
            $klarnaHandlerPlugin = new KlarnaHandlerPlugin();

            $paymentMethodHandler->add($klarnaHandlerPlugin, KlarnaConstants::PAYMENT_METHOD_INVOICE);
            $paymentMethodHandler->add($klarnaHandlerPlugin, KlarnaConstants::PAYMENT_METHOD_INSTALLMENT);

            return $paymentMethodHandler;
        });

        return $container;
    }

    /**
     * @param \Generated\Shared\Transfer\QuoteTransfer $quoteTransfer
     * @param bool $create
     *
     * @return \Spryker\Yves\StepEngine\Dependency\Plugin\Form\SubFormPluginInterface[]
     */
    protected function getPaymentMethodsSubForms(QuoteTransfer $quoteTransfer, $create = true)
    {
        $pluginCountryFactory = new PluginCountryFactory();
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
            )
        ) {
            return [];
        }
        $subFormsCreator = $pluginCountryFactory
            ->createSubFormsCreator($quoteTransfer->getBillingAddress()->getIso2Code());

        $paymentMethodsSubForms = $subFormsCreator->createPaymentMethodsSubForms($quoteTransfer, ['create' => $create]);

        return $paymentMethodsSubForms;
    }

}
