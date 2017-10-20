<?php

/**
 * MIT License
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace SprykerEco\Yves\Klarna;

use Spryker\Shared\Kernel\Store;
use Spryker\Yves\Kernel\AbstractFactory;
use SprykerEco\Yves\Klarna\Form\DataProvider\InstallmentDataProvider;
use SprykerEco\Yves\Klarna\Form\DataProvider\InvoiceDataProvider;
use SprykerEco\Yves\Klarna\Form\InstallmentSubForm;
use SprykerEco\Yves\Klarna\Form\InvoiceSubForm;
use SprykerEco\Yves\Klarna\Handler\KlarnaHandler;

/**
 * Class KlarnaFactory
 *
 * @package SprykerEco\Yves\Klarna
 *
 * @author Daniel Bohnhardt <daniel.bohnhardt@twt.de>
 */
class KlarnaFactory extends AbstractFactory
{
    /**
     * @param string $countryIso2
     *
     * @return \SprykerEco\Yves\Klarna\Form\InvoiceSubForm
     */
    public function createInvoiceForm($countryIso2)
    {
        return new InvoiceSubForm($countryIso2);
    }

    /**
     * @param string $countryIso2
     * @param \Generated\Shared\Transfer\QuoteTransfer $quoteTransfer
     * @param \SprykerEco\Yves\Klarna\Form\DataProvider\InstallmentDataProvider $subFormDataProvider
     *
     * @return \SprykerEco\Yves\Klarna\Form\InstallmentSubForm
     */
    public function createInstallmentForm($countryIso2, $quoteTransfer, $subFormDataProvider)
    {
        return new InstallmentSubForm($countryIso2, $quoteTransfer, $subFormDataProvider);
    }

    /**
     * @return \SprykerEco\Yves\Klarna\Handler\KlarnaHandler
     */
    public function createKlarnaHandler()
    {
        return new KlarnaHandler($this->getKlarnaClient(), $this->getFlashMessenger());
    }

    /**
     * @return \SprykerEco\Client\Klarna\KlarnaClientInterface
     */
    public function getKlarnaClient()
    {
        return $this->getProvidedDependency(KlarnaDependencyProvider::CLIENT_KLARNA);
    }

    /**
     * @return \Spryker\Yves\Messenger\FlashMessenger\FlashMessengerInterface
     */
    public function getFlashMessenger()
    {
        return $this->getApplication()['flash_messenger'];
    }

    /**
     * @return \Spryker\Yves\Kernel\Application
     */
    protected function getApplication()
    {
        return $this->getProvidedDependency(KlarnaDependencyProvider::PLUGIN_APPLICATION);
    }

    /**
     * @return \SprykerEco\Yves\Klarna\Form\DataProvider\InvoiceDataProvider
     */
    public function createInvoiceDataProvider()
    {
        return new InvoiceDataProvider();
    }

    /**
     * @return \SprykerEco\Yves\Klarna\Form\DataProvider\InstallmentDataProvider
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
     */
    public function getCartClient()
    {
        return $this->getProvidedDependency(KlarnaDependencyProvider::CLIENT_CART);
    }

    /**
     * @return \Spryker\Client\Glossary\GlossaryClient
     */
    public function getTranslatorClient()
    {
        return $this->getProvidedDependency(KlarnaDependencyProvider::CLIENT_GLOSSARY);
    }

    /**
     * @return \Spryker\Shared\Kernel\Store
     */
    public function getStore()
    {
        return Store::getInstance();
    }
}
