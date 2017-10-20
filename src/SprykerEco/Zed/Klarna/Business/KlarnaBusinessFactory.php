<?php

/**
 * MIT License
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace SprykerEco\Zed\Klarna\Business;

use Klarna_Checkout_Connector;
use Orm\Zed\Klarna\Persistence\SpyPaymentKlarna;
use Spryker\Zed\Kernel\Business\AbstractBusinessFactory;
use SprykerEco\Zed\Klarna\Business\Address\AddressUpdater;
use SprykerEco\Zed\Klarna\Business\Api\Adapter\Klarna;
use SprykerEco\Zed\Klarna\Business\Api\Handler\KlarnaApi;
use SprykerEco\Zed\Klarna\Business\Api\Handler\KlarnaCheckoutApi;
use SprykerEco\Zed\Klarna\Business\Log\Log;
use SprykerEco\Zed\Klarna\Business\Order\SalesHelper;
use SprykerEco\Zed\Klarna\Business\Order\Saver;
use SprykerEco\Zed\Klarna\Business\Request\CheckoutServiceRequest;
use SprykerEco\Zed\Klarna\Business\Request\GetAddressesRequest;
use SprykerEco\Zed\Klarna\Business\Request\InstallmentRequest;
use SprykerEco\Zed\Klarna\Business\Request\KlarnaCheckout;
use SprykerEco\Zed\Klarna\Business\Request\Mapper\PClassRequestTransferMapper;
use SprykerEco\Zed\Klarna\Business\Request\ReserveAmount;
use SprykerEco\Zed\Klarna\Business\Response\Mapper\AddressesResponseTransferMapper;
use SprykerEco\Zed\Klarna\Business\Response\Mapper\CheckoutServiceResponseTransferMapper;
use SprykerEco\Zed\Klarna\Business\Response\Mapper\InstallmentTransferMapper;
use SprykerEco\Zed\Klarna\KlarnaDependencyProvider;

/**
 * Class KlarnaBusinessFactory
 *
 * @package SprykerEco\Zed\Klarna\Business
 * @method \SprykerEco\Zed\Klarna\KlarnaConfig getConfig()
 * @method \SprykerEco\Zed\Klarna\Persistence\KlarnaQueryContainerInterface getQueryContainer()
 * @author Daniel Bohnhardt <daniel.bohnhardt@twt.de>
 */
class KlarnaBusinessFactory extends AbstractBusinessFactory
{
    /**
     * @return \SprykerEco\Zed\Klarna\Business\Order\SaverInterface
     */
    public function createOrderSaver()
    {
        return new Saver();
    }

    /**
     * @return \SprykerEco\Zed\Klarna\Business\Api\Handler\KlarnaApi
     */
    public function createKlarnaApi()
    {
        $config = $this->getConfig();

        return new KlarnaApi(
            $this->createAdapter(),
            $config->getEid(),
            $config->getSharedSecret(),
            $config->isTestMode(),
            $config->getMailMode(),
            $config->getPclassStoreType(),
            $config->getPclassStoreUri(),
            $this->getLocaleFacade(),
            $this->getMoneyFacade()
        );
    }

    /**
     * @return \SprykerEco\Zed\Klarna\Business\Api\Handler\KlarnaCheckoutApi
     */
    public function createKlarnaCheckoutApi()
    {
        $config = $this->getConfig();

        return new KlarnaCheckoutApi(
            $config->getEid(),
            $config->getCheckoutConfirmationUri(),
            $config->getCheckoutPushUri(),
            $config->getCheckoutTermsUri(),
            $config->getCheckoutUri(),
            $this->getKlarnaCheckoutConnector()
        );
    }

    /**
     * @return \Klarna_Checkout_ConnectorInterface
     */
    public function getKlarnaCheckoutConnector()
    {
        $config = $this->getConfig();

        return Klarna_Checkout_Connector::create(
            $config->getSharedSecret(),
            ($config->isTestMode()) ?
                  Klarna_Checkout_Connector::BASE_TEST_URL
                : Klarna_Checkout_Connector::BASE_URL
        );
    }

    /**
     * @return \SprykerEco\Zed\Klarna\Business\Request\ReserveAmount
     */
    public function createReserveAmount()
    {
        return new ReserveAmount(
            $this->createKlarnaApi()
        );
    }

    /**
     * @return \Klarna
     */
    public function createAdapter()
    {
        return new Klarna();
    }

    /**
     * @return \SprykerEco\Zed\Klarna\Business\Request\CheckoutServiceRequest
     */
    public function createCheckoutServiceRequest()
    {
        return new CheckoutServiceRequest(
            $this->createKlarnaApi(),
            $this->createCheckoutServiceResponseTransferMapper()
        );
    }

    /**
     * @return \SprykerEco\Zed\Klarna\Business\Request\InstallmentRequest
     */
    public function createInstallment()
    {
        return new InstallmentRequest(
            $this->createKlarnaApi(),
            $this->createPClassRequestTransferMapper(),
            $this->createInstallmentTransferMapper()
        );
    }

    /**
     * @return \SprykerEco\Zed\Klarna\Business\Request\GetAddressesRequest
     */
    public function createGetAddressesRequest()
    {
        return new GetAddressesRequest(
            $this->createKlarnaApi(),
            $this->createAddressesResponseTransferMapper()
        );
    }

    /**
     * @return \SprykerEco\Zed\Klarna\Business\Log\Log
     */
    public function createPaymentLog()
    {
        return new Log(
            $this->getQueryContainer()
        );
    }

    /**
     * @return \SprykerEco\Zed\Klarna\Business\Request\KlarnaCheckout
     */
    public function createKlarnaCheckout()
    {
        return new KlarnaCheckout(
            $this->createKlarnaCheckoutApi(),
            $this->getCheckoutFacade()
        );
    }

    /**
     * @return \SprykerEco\Zed\Klarna\Dependency\Facade\KlarnaToCheckoutBridgeInterface
     */
    public function getCheckoutFacade()
    {
        return $this->getProvidedDependency(KlarnaDependencyProvider::FACADE_CHECKOUT);
    }

    /**
     * @return \SprykerEco\Zed\Klarna\Dependency\Facade\KlarnaToLocaleInterface
     */
    public function getLocaleFacade()
    {
        return $this->getProvidedDependency(KlarnaDependencyProvider::FACADE_LOCALE);
    }

    /**
     * @return \SprykerEco\Zed\Klarna\Business\Order\SalesHelper
     */
    protected function createSalesHelper()
    {
        return new SalesHelper(
            $this->getQueryContainer()
        );
    }

    /**
     * @param int $salesOrderId
     *
     * @return string
     */
    public function getInvoicePdfUrl($salesOrderId)
    {
        return $this->createSalesHelper()->getInvoicePdfUrl(
            $this->getConfig()->getPdfUrlPattern(),
            $salesOrderId
        );
    }

    /**
     * @param int $salesOrderId
     *
     * @return array
     */
    public function getKlarnaPaymentById($salesOrderId)
    {
        return $this->createSalesHelper()->getKlarnaPaymentById($salesOrderId);
    }

    /**
     * @return \Spryker\Yves\Application\Application
     */
    public function getApplication()
    {
        return $this->getProvidedDependency(KlarnaDependencyProvider::PLUGIN_APPLICATION);
    }

    /**
     * @param \Orm\Zed\Klarna\Persistence\SpyPaymentKlarna $spyPayment
     *
     * @return \SprykerEco\Zed\Klarna\Business\Address\AddressUpdater
     */
    public function createAddressUpdater(SpyPaymentKlarna $spyPayment)
    {
        return new AddressUpdater(
            $this->createKlarnaApi(),
            $spyPayment
        );
    }

    /**
     * @return \SprykerEco\Zed\Klarna\Business\Request\Mapper\PClassRequestTransferMapper
     */
    public function createPClassRequestTransferMapper()
    {
        return new PClassRequestTransferMapper();
    }

    /**
     * @return \SprykerEco\Zed\Klarna\Business\Response\Mapper\CheckoutServiceResponseTransferMapper
     */
    public function createCheckoutServiceResponseTransferMapper()
    {
        return new CheckoutServiceResponseTransferMapper();
    }

    /**
     * @return \SprykerEco\Zed\Klarna\Business\Response\Mapper\InstallmentTransferMapper
     */
    public function createInstallmentTransferMapper()
    {
        return new InstallmentTransferMapper($this->getMoneyFacade());
    }

    /**
     * @return \SprykerEco\Zed\Klarna\Business\Response\Mapper\AddressesResponseTransferMapper
     */
    public function createAddressesResponseTransferMapper()
    {
        return new AddressesResponseTransferMapper();
    }

    /**
     * @return \SprykerEco\Zed\Klarna\Dependency\Facade\KlarnaToMoneyInterface
     */
    protected function getMoneyFacade()
    {
        return $this->getProvidedDependency(KlarnaDependencyProvider::FACADE_MONEY);
    }
}
