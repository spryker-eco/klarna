<?php

/**
 * MIT License
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace SprykerEcoTest\Zed\Klarna\Business;

use SprykerEcoTest\Zed\Klarna\Business\Api\Mock\KlarnaCaptureMock;
use SprykerEcoTest\Zed\Klarna\Business\Api\Mock\KlarnaRefundMock;
use Generated\Shared\Transfer\OrderTransfer;
use Generated\Shared\Transfer\TotalsTransfer;
use KlarnaException;
use Orm\Zed\Country\Persistence\SpyCountryQuery;
use Orm\Zed\Customer\Persistence\Map\SpyCustomerTableMap;
use Orm\Zed\Customer\Persistence\SpyCustomerQuery;
use Orm\Zed\Klarna\Persistence\Map\SpyPaymentKlarnaTableMap;
use Orm\Zed\Klarna\Persistence\SpyPaymentKlarna;
use Orm\Zed\Klarna\Persistence\SpyPaymentKlarnaOrderItem;
use Orm\Zed\Oms\Persistence\SpyOmsOrderItemState;
use Orm\Zed\Sales\Persistence\SpySalesOrder;
use Orm\Zed\Sales\Persistence\SpySalesOrderAddress;
use Orm\Zed\Sales\Persistence\SpySalesOrderItem;
use SprykerEco\Shared\Klarna\KlarnaConstants;

/**
 * Class KlarnaOmsFacadeTest
 *
 * @package SprykerEcoTest\Zed\Klarna\Business
 * @author Daniel Bohnhardt <daniel.bohnhardt@twt.de>
 */
class KlarnaOmsFacadeTest extends AbstractFacadeTest
{

    /**
     * @var \Orm\Zed\Sales\Persistence\SpySalesOrderItem
     */
    protected $orderItem;

    /**
     * @var \Orm\Zed\Sales\Persistence\SpySalesOrder
     */
    private $orderEntity;

    /**
     * @var \Orm\Zed\Klarna\Persistence\SpyPaymentKlarna
     */
    private $paymentEntity;

    /**
     * @author Daniel Bohnhardt <daniel.bohnhardt@twt.de>
     *
     * @return void
     */
    public function testCapturePayment()
    {
        $this->setUpSalesOrderTestData();
        $this->setUpPaymentTestData();
        $orderTransfer = $this->createOrderTransfer();
        $paymentEntity = $this->getPaymentEntity();
        $adapterMock = new KlarnaCaptureMock();
        $facade = $this->generateFacade($adapterMock);
        $response = $facade->capturePayment($paymentEntity, $orderTransfer);

        $this->assertTrue(is_array($response));
        $this->assertCount(2, $response);
        $this->assertSame('riskStatus', $response[0]);
        $this->assertSame('invoiceNumber', $response[1]);
    }

    /**
     * Test part activation.
     *
     * @author Daniel Bohnhardt <daniel.bohnhardt@twt.de>
     *
     * @return void
     */
    public function testCapturePartPayment()
    {
        $this->setUpSalesOrderTestData();
        $this->setUpPaymentTestData();
        $orderTransfer = $this->createOrderTransfer();
        $paymentEntity = $this->getPaymentEntity();

        $orderItem = new SpySalesOrderItem();
        $orderItem->setQuantity(1);
        $orderItem->setSku(123456);

        $orderItems = [$orderItem];

        $adapterMock = new KlarnaCaptureMock();
        $facade = $this->generateFacade($adapterMock);
        $response = $facade->capturePartPayment($orderItems, $paymentEntity, $orderTransfer);

        $this->assertTrue(is_array($response));
        $this->assertCount(2, $response);
        $this->assertSame('riskStatus', $response[0]);
        $this->assertSame('invoiceNumber', $response[1]);
    }

    /**
     * @author Daniel Bohnhardt <daniel.bohnhardt@twt.de>
     *
     * @return void
     */
    public function testCapturePaymentFailed()
    {
        $this->setUpSalesOrderTestData();
        $this->setUpPaymentTestData();
        $orderTransfer = $this->createOrderTransfer();
        $paymentEntity = $this->getPaymentEntity();
        $adapterMock = new KlarnaCaptureMock();
        $adapterMock->setException(new KlarnaException('test Exception'));
        $facade = $this->generateFacade($adapterMock);
        $response = $facade->capturePayment($paymentEntity, $orderTransfer);

        $this->assertTrue(is_array($response));
        $this->assertCount(3, $response);
        $this->assertSame(0, $response[0]);
        $this->assertSame('test Exception', $response[2]);
    }

    /**
     * @author Daniel Bohnhardt <daniel.bohnhardt@twt.de>
     *
     * @return void
     */
    public function testRefundPayment()
    {
        $this->setUpSalesOrderTestData();
        $this->setUpPaymentTestData();
        $paymentEntity = $this->getPaymentEntity();
        $adapterMock = new KlarnaRefundMock();
        $facade = $this->generateFacade($adapterMock);
        $response = $facade->refundPayment($paymentEntity);

        $this->assertSame('invoiceNumber', $response);
    }

    /**
     * @author Daniel Bohnhardt <daniel.bohnhardt@twt.de>
     *
     * @return void
     */
    public function testPartRefundPayment()
    {
        $this->setUpSalesOrderTestData();
        $this->setUpPaymentTestData();
        $paymentEntity = $this->getPaymentEntity();

        $orderItem = $this->getOrderItemEntity();

        $orderItems = [$orderItem];

        $adapterMock = new KlarnaRefundMock();
        $facade = $this->generateFacade($adapterMock);
        $response = $facade->refundPartPayment($orderItems, $paymentEntity);

        $this->assertSame('invoicePartNumber', $response);
    }

    /**
     * @author Daniel Bohnhardt <daniel.bohnhardt@twt.de>
     *
     * @expectedException \KlarnaException
     * @expectedExceptionMessage test
     *
     * @return void
     */
    public function testRefundPaymentFailed()
    {
        $this->setUpSalesOrderTestData();
        $this->setUpPaymentTestData();
        $paymentEntity = $this->getPaymentEntity();
        $adapterMock = new KlarnaRefundMock();
        $adapterMock->setException(new KlarnaException('test'));
        $facade = $this->generateFacade($adapterMock);
        $facade->refundPayment($paymentEntity);
    }

    /**
     * @return void
     */
    protected function setUpSalesOrderTestData()
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

        $customer = (new SpyCustomerQuery())
            ->filterByFirstName('John')
            ->filterByLastName('Doe')
            ->filterByEmail('john@doe.com')
            ->filterByDateOfBirth('1970-01-01')
            ->filterByGender(SpyCustomerTableMap::COL_GENDER_MALE)
            ->filterByCustomerReference('klarna-test')
            ->findOneOrCreate();

        $customer->save();

        $this->orderEntity = (new SpySalesOrder())
            ->setEmail('john@doe.com')
            ->setIsTest(true)
            ->setFkSalesOrderAddressBilling($billingAddress->getIdSalesOrderAddress())
            ->setFkSalesOrderAddressShipping($billingAddress->getIdSalesOrderAddress())
            ->setCustomer($customer)
            ->setOrderReference('foo-bar-baz-2');

        $this->orderEntity->save();

        $orderItemState = (new SpyOmsOrderItemState())->setName('a');
        $orderItemState->save();

        $this->orderItem = (new SpySalesOrderItem())
            ->setSku(123)
            ->setName('b')
            ->setGrossPrice(20)
            ->setFkSalesOrder($this->orderEntity->getIdSalesOrder())
            ->setFkOmsOrderItemState($orderItemState->getIdOmsOrderItemState());
        $this->orderItem->save();
    }

    /**
     * @return \Generated\Shared\Transfer\OrderTransfer
     */
    protected function createOrderTransfer()
    {
        $orderTransfer = new OrderTransfer();
        $totalTransfer = new TotalsTransfer();
        $totalTransfer->setGrandTotal(1000);
        $orderTransfer->setTotals($totalTransfer);
        $orderTransfer->setIdSalesOrder($this->orderEntity->getIdSalesOrder());

        return $orderTransfer;
    }

    /**
     * @return void
     */
    private function setUpPaymentTestData()
    {
        $this->paymentEntity = (new SpyPaymentKlarna())
            ->setFkSalesOrder($this->getOrderEntity()->getIdSalesOrder())
            ->setAccountBrand(KlarnaConstants::BRAND_INVOICE)
            ->setClientIp('127.0.0.1')
            ->setFirstName('Jane')
            ->setLastName('Doe')
            ->setDateOfBirth('1970-01-02')
            ->setEmail('jane@family-doe.org')
            ->setGender(SpyPaymentKlarnaTableMap::COL_GENDER_MALE)
            ->setSalutation(SpyPaymentKlarnaTableMap::COL_SALUTATION_MR)
            ->setCountryIso2Code('de')
            ->setCity('Berlin')
            ->setStreet('Straße des 17. Juni 135')
            ->setZipCode('10623')
            ->setLanguageIso2Code('de')
            ->setCurrencyIso3Code('EUR');
        $this->paymentEntity->save();

        $orderItem = new SpyPaymentKlarnaOrderItem();
        $orderItem->setFkPaymentKlarna($this->getPaymentEntity()->getIdPaymentKlarna());
        $orderItem->setFkSalesOrderItem($this->getOrderItemEntity()->getIdSalesOrderItem());
        $orderItem->setInvoiceId('invoiceId');
        $orderItem->save();
    }

    /**
     * @return \Orm\Zed\Sales\Persistence\SpySalesOrder
     */
    protected function getOrderEntity()
    {
        return $this->orderEntity;
    }

    /**
     * @return \Orm\Zed\Klarna\Persistence\SpyPaymentKlarna
     */
    protected function getPaymentEntity()
    {
        return $this->paymentEntity;
    }

    /**
     * @return \Orm\Zed\Sales\Persistence\SpySalesOrderItem
     */
    protected function getOrderItemEntity()
    {
        return $this->orderItem;
    }

}
