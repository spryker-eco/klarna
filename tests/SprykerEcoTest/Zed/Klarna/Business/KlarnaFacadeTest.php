<?php

/**
 * MIT License
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace SprykerEcoTest\Zed\Klarna\Business;

use Generated\Shared\Transfer\AddressTransfer;
use Generated\Shared\Transfer\CheckoutResponseTransfer;
use Generated\Shared\Transfer\CustomerTransfer;
use Generated\Shared\Transfer\ItemTransfer;
use Generated\Shared\Transfer\KlarnaPaymentTransfer;
use Generated\Shared\Transfer\PaymentTransfer;
use Generated\Shared\Transfer\QuoteTransfer;
use Generated\Shared\Transfer\SaveOrderTransfer;
use Generated\Shared\Transfer\ShipmentMethodTransfer;
use Generated\Shared\Transfer\ShipmentTransfer;
use Orm\Zed\Country\Persistence\SpyCountryQuery;
use Orm\Zed\Customer\Persistence\Map\SpyCustomerTableMap;
use Orm\Zed\Customer\Persistence\SpyCustomer;
use Orm\Zed\Klarna\Persistence\Map\SpyPaymentKlarnaTableMap;
use Orm\Zed\Oms\Persistence\SpyOmsOrderItemState;
use Orm\Zed\Oms\Persistence\SpyOmsOrderProcess;
use Orm\Zed\Sales\Persistence\SpySalesOrder;
use Orm\Zed\Sales\Persistence\SpySalesOrderAddress;
use Orm\Zed\Sales\Persistence\SpySalesOrderItem;
use PHPUnit_Framework_TestResult;
use SprykerEco\Shared\Klarna\KlarnaConfig;
use SprykerEcoTest\Zed\Klarna\Business\Api\Mock\KlarnaReserveMock;

/**
 * Class KlarnaFacadeTest
 *
 * @package SprykerEcoTest\Zed\Klarna\Business
 *
 * @author Daniel Bohnhardt <daniel.bohnhardt@twt.de>
 */
class KlarnaFacadeTest extends AbstractFacadeTest
{
    /**
     * @author Daniel Bohnhardt <daniel.bohnhardt@twt.de>
     *
     * @return void
     */
    public function testReserveAmount()
    {
        $checkoutResponseTransfer = $this->createCheckoutResponse();
        $quoteTransfer = $this->getQuoteTransfer($checkoutResponseTransfer);
        $adapterMock = new KlarnaReserveMock();
        $facade = $this->generateFacade($adapterMock);
        $return = $facade->reserveAmount($quoteTransfer);

        $this->assertInstanceOf(KlarnaReserveAmountResponseTransfer::class, $return);

        $this->assertEquals('testStatus', $return->getStatus());
        $this->assertEquals('testRefNo', $return->getReservationNo());
        $this->assertNull($return->getError());
    }

    /**
     * @param \Generated\Shared\Transfer\CheckoutResponseTransfer $checkoutResponseTransfer
     *
     * @return \Generated\Shared\Transfer\QuoteTransfer
     */
    private function getQuoteTransfer(CheckoutResponseTransfer $checkoutResponseTransfer)
    {
        $orderEntity = $this->createOrderEntity();

        $paymentAddressTransfer = new AddressTransfer();
        $email = 'testst@tewst.com';
        $paymentAddressTransfer
            ->setIso2Code('DE')
            ->setEmail($email)
            ->setFirstName('John')
            ->setLastName('Doe')
            ->setCellPhone('+40 175 0815')
            ->setPhone('+30 0815')
            ->setAddress1('Straße des 17. Juni')
            ->setAddress2('135')
            ->setZipCode('10623')
            ->setSalutation(SpyPaymentKlarnaTableMap::COL_SALUTATION_MR)
            ->setCity('Berlin');

        $klarnaPaymentTransfer = new KlarnaPaymentTransfer();
        $klarnaPaymentTransfer
            ->setPhone('+30 0815')
            ->setEmail($email)
            ->setGender(SpyPaymentKlarnaTableMap::COL_GENDER_MALE)
            ->setDateOfBirth('1970-01-02')
            ->setClientIp('127.0.0.1')
            ->setAccountBrand(KlarnaConfig::BRAND_INVOICE)
            ->setLanguageIso2Code('DE')
            ->setCurrencyIso3Code('EUR')
            ->setAddress($paymentAddressTransfer);

        $quoteTransfer = new QuoteTransfer();

        $customerTransfer = new CustomerTransfer();
        $customerTransfer->setEmail($email);
        $customerTransfer->setIsGuest(true);
        $quoteTransfer->setBillingAddress($paymentAddressTransfer);
        $quoteTransfer->setCustomer($customerTransfer);

        $shipmentMethodTransfer = new ShipmentMethodTransfer();

        $shipmentTransfer = new ShipmentTransfer();
        $shipmentTransfer->setMethod($shipmentMethodTransfer);

        $quoteTransfer->setShipment($shipmentTransfer);

        $checkoutResponseTransfer->getSaveOrder()->setIdSalesOrder($orderEntity->getIdSalesOrder());

        $paymentTransfer = new PaymentTransfer();
        $paymentTransfer->setKlarna($klarnaPaymentTransfer);

        $quoteTransfer->setPayment($paymentTransfer);

        foreach ($orderEntity->getItems() as $orderItemEntity) {
            $itemTransfer = new ItemTransfer();
            $itemTransfer
                ->setName($orderItemEntity->getName())
                ->setQuantity($orderItemEntity->getQuantity())
                ->setUnitGrossPrice($orderItemEntity->getGrossPrice())
                ->setFkSalesOrder($orderItemEntity->getFkSalesOrder())
                ->setIdSalesOrderItem($orderItemEntity->getIdSalesOrderItem());
            $checkoutResponseTransfer->getSaveOrder()->addOrderItem($itemTransfer);
        }

        return $quoteTransfer;
    }

    /**
     * @return \Orm\Zed\Sales\Persistence\SpySalesOrder
     */
    private function createOrderEntity()
    {
        $country = SpyCountryQuery::create()->findOneByIso2Code('DE');

        $billingAddress = (new SpySalesOrderAddress())
            ->setFkCountry($country->getIdCountry())
            ->setFirstName('John')
            ->setLastName('Doe')
            ->setAddress1('Straße des 17. Juni 135')
            ->setCity('Berlin')
            ->setZipCode('10623');
        $billingAddress->save();

        $customer = (new SpyCustomer())
            ->setFirstName('John')
            ->setLastName('Doe')
            ->setEmail('john@doe.com')
            ->setDateOfBirth('1970-01-01')
            ->setGender(SpyCustomerTableMap::COL_GENDER_MALE)
            ->setCustomerReference('klarna-test');
        $customer->save();

        $orderEntity = (new SpySalesOrder())
            ->setEmail('john@doe.com')
            ->setIsTest(true)
            ->setFkSalesOrderAddressBilling($billingAddress->getIdSalesOrderAddress())
            ->setFkSalesOrderAddressShipping($billingAddress->getIdSalesOrderAddress())
            ->setCustomer($customer)
            ->setOrderReference('foo-bar-baz-2');
        $orderEntity->save();

        $this->createOrderItemEntity($orderEntity->getIdSalesOrder());

        return $orderEntity;
    }

    /**
     * @param int $idSalesOrder
     *
     * @return \Orm\Zed\Sales\Persistence\SpySalesOrderItem
     */
    private function createOrderItemEntity($idSalesOrder)
    {
        $stateEntity = $this->createOrderItemStateEntity();
        $processEntity = $this->createOrderProcessEntity();

        $orderItemEntity = new SpySalesOrderItem();
        $orderItemEntity
            ->setFkSalesOrder($idSalesOrder)
            ->setFkOmsOrderItemState($stateEntity->getIdOmsOrderItemState())
            ->setFkOmsOrderProcess($processEntity->getIdOmsOrderProcess())
            ->setName('test product')
            ->setSku('1324354657687980')
            ->setGrossPrice(1000)
            ->setQuantity(1);
        $orderItemEntity->save();

        return $orderItemEntity;
    }

    /**
     * @return \Orm\Zed\Oms\Persistence\SpyOmsOrderItemState
     */
    private function createOrderItemStateEntity()
    {
        $stateEntity = new SpyOmsOrderItemState();
        $stateEntity->setName('test item state');
        $stateEntity->save();

        return $stateEntity;
    }

    /**
     * @return \Orm\Zed\Oms\Persistence\SpyOmsOrderProcess
     */
    private function createOrderProcessEntity()
    {
        $processEntity = new SpyOmsOrderProcess();
        $processEntity->setName('test process');
        $processEntity->save();

        return $processEntity;
    }

    /**
     * @return \Generated\Shared\Transfer\CheckoutResponseTransfer
     */
    protected function createCheckoutResponse()
    {
        $checkoutResponseTransfer = new CheckoutResponseTransfer();
        $saveOrderTransfer = new SaveOrderTransfer();
        $checkoutResponseTransfer->setSaveOrder($saveOrderTransfer);

        return $checkoutResponseTransfer;
    }

    /**
     * Count elements of an object
     *
     * @link http://php.net/manual/en/countable.count.php
     *
     * @since 5.1.0
     *
     * @return int The custom count as an integer.
     * </p>
     * <p>
     * The return value is cast to an integer.
     */
    public function count()
    {
        // TODO: Implement count() method.
    }

    /**
     * Runs a test and collects its result in a TestResult instance.
     *
     * @param \PHPUnit_Framework_TestResult|null $result
     *
     * @return \PHPUnit_Framework_TestResult
     */
    public function run(PHPUnit_Framework_TestResult $result = null)
    {
        // TODO: Implement run() method.
    }
}
