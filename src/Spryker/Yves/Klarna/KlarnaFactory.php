<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Yves\Klarna;

use Pyz\Yves\Application\Plugin\Pimple;
use Spryker\Shared\Kernel\Store;
use Spryker\Yves\Kernel\AbstractFactory;
use Spryker\Yves\Klarna\Form\DataProvider\InstallmentDataProvider;
use Spryker\Yves\Klarna\Form\DataProvider\InvoiceDataProvider;
use Spryker\Yves\Klarna\Form\InstallmentSubForm;
use Spryker\Yves\Klarna\Form\InvoiceSubForm;
use Spryker\Yves\Klarna\Handler\KlarnaHandler;

/**
 * Class KlarnaFactory
 *
 * @package Spryker\Yves\Klarna
 *
 * @author Daniel Bohnhardt <daniel.bohnhardt@twt.de>
 */
class KlarnaFactory extends AbstractFactory
{

    /**
     * @param string $countryIso2
     *
     * @return \Spryker\Yves\Klarna\Form\InvoiceSubForm
     */
    public function createInvoiceForm($countryIso2)
    {
        return new InvoiceSubForm($countryIso2);
    }

    /**
     * @param string $countryIso2
     * @param \Generated\Shared\Transfer\QuoteTransfer $quoteTransfer
     * @param \Spryker\Yves\Klarna\Form\DataProvider\InstallmentDataProvider $subFormDataProvider
     *
     * @return \Spryker\Yves\Klarna\Form\InstallmentSubForm
     * @author Daniel Bohnhardt <daniel.bohnhardt@twt.de>
     */
    public function createInstallmentForm($countryIso2, $quoteTransfer, $subFormDataProvider)
    {
        return new InstallmentSubForm($countryIso2, $quoteTransfer, $subFormDataProvider);
    }

    /**
     * @return \Spryker\Yves\Klarna\Handler\KlarnaHandler
     */
    public function createKlarnaHandler()
    {
        return new KlarnaHandler($this->getKlarnaClient(), $this->getFlashMessenger());
    }

    /**
     * @return \Spryker\Client\Klarna\KlarnaClientInterface
     */
    public function getKlarnaClient()
    {
        return $this->getLocator()->klarna()->client();
    }

    /**
     * @return \Pyz\Yves\Application\Business\Model\FlashMessengerInterface
     */
    public function getFlashMessenger()
    {
        return $this->createApplication()['flash_messenger'];
    }

    /**
     * @return \Spryker\Yves\Application\Application
     */
    protected function createApplication()
    {
        return (new Pimple())->getApplication();
    }

    /**
     * @return \Spryker\Yves\Klarna\Form\DataProvider\InvoiceDataProvider
     * @author Daniel Bohnhardt <daniel.bohnhardt@twt.de>
     */
    public function createInvoiceDataProvider()
    {
        return new InvoiceDataProvider();
    }

    /**
     * @return \Spryker\Yves\Klarna\Form\DataProvider\InstallmentDataProvider
     * @author Daniel Bohnhardt <daniel.bohnhardt@twt.de>
     */
    public function createInstallmentDataProvider()
    {
        return new InstallmentDataProvider(
            $this->getKlarnaClient(),
            $this->getTranslatorClient(),
            $this->getStore()
        );
    }

    /**
     * @return \Spryker\Client\Cart\CartClientInterface
     * @author Daniel Bohnhardt <daniel.bohnhardt@twt.de>
     */
    public function getCartClient()
    {
        return $this->getLocator()->cart()->client();
    }

    /**
     * @return \Spryker\Client\Glossary\GlossaryClient
     * @author Daniel Bohnhardt <daniel.bohnhardt@twt.de>
     */
    public function getTranslatorClient()
    {
        return $this->getLocator()->glossary()->client();
    }

    /**
     * @return \Spryker\Shared\Kernel\Store
     * @author Daniel Bohnhardt <daniel.bohnhardt@twt.de>
     */
    public function getStore()
    {
        return Store::getInstance();
    }

}
