<?php

/**
 * MIT License
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Functional\SprykerEco\Zed\Klarna\Business\Order;

use Codeception\TestCase\Test;
use Generated\Shared\Transfer\AddressTransfer;
use Generated\Shared\Transfer\CheckoutResponseTransfer;
use Generated\Shared\Transfer\CustomerTransfer;
use Generated\Shared\Transfer\ItemTransfer;
use Generated\Shared\Transfer\KlarnaPaymentTransfer;
use Generated\Shared\Transfer\PaymentTransfer;
use Generated\Shared\Transfer\QuoteTransfer;
use Generated\Shared\Transfer\SaveOrderTransfer;
use Orm\Zed\Country\Persistence\SpyCountryQuery;
use Orm\Zed\Customer\Persistence\Map\SpyCustomerTableMap;
use Orm\Zed\Customer\Persistence\SpyCustomer;
use Orm\Zed\Klarna\Persistence\Map\SpyPaymentKlarnaTableMap;
use Orm\Zed\Klarna\Persistence\SpyPaymentKlarna;
use Orm\Zed\Klarna\Persistence\SpyPaymentKlarnaQuery;
use Orm\Zed\Klarna\Persistence\SpyPaymentKlarnaTransactionStatusLog;
use Orm\Zed\Klarna\Persistence\SpyPaymentKlarnaTransactionStatusLogQuery;
use Orm\Zed\Oms\Persistence\SpyOmsOrderItemState;
use Orm\Zed\Oms\Persistence\SpyOmsOrderProcess;
use Orm\Zed\Sales\Persistence\SpySalesOrder;
use Orm\Zed\Sales\Persistence\SpySalesOrderAddress;
use Orm\Zed\Sales\Persistence\SpySalesOrderItem;
use Orm\Zed\Sales\Persistence\SpySalesOrderItemBundle;
use Orm\Zed\Sales\Persistence\SpySalesOrderItemBundleItem;
use SprykerEco\Shared\Klarna\KlarnaConstants;
use SprykerEco\Zed\Klarna\Business\KlarnaBusinessFactory;
use SprykerEco\Zed\Klarna\Business\Order\Saver;

/**
 * Class SaverTest
 *
 * @package Functional\SprykerEco\Zed\Klarna\Business\Order
 * @author Daniel Bohnhardt <daniel.bohnhardt@twt.de>
 */
class SaverTest extends Test
{

    /**
     * @return void
     */
    public function testSaveOrderPaymentCreatesPersistentPaymentData()
    {
        $checkoutResponseTransfer = $this->createCheckoutResponse();
        $quoteTransfer = $this->getQuoteTransfer($checkoutResponseTransfer);
        $orderManager = new Saver($this->getKlarnaBusinessFactory());

        $orderManager->saveOrderPayment($quoteTransfer, $checkoutResponseTransfer);

        $paymentEntity = SpyPaymentKlarnaQuery::create()->findOneByFkSalesOrder(
            $checkoutResponseTransfer->getSaveOrder()->getIdSalesOrder()
        );
        $this->assertInstanceOf(SpyPaymentKlarna::class, $paymentEntity);

        $paymentOrderItemEntities = $paymentEntity->getSpyPaymentKlarnaOrderItems();
        $this->assertCount(1, $paymentOrderItemEntities);

        $statusLogEntity = SpyPaymentKlarnaTransactionStatusLogQuery::create()->findOneByFkPaymentKlarna(
            $paymentEntity->getIdPaymentKlarna()
        );

        $this->assertInstanceOf(SpyPaymentKlarnaTransactionStatusLog::class, $statusLogEntity);
        $this->assertSame('1', $statusLogEntity->getProcessingStatus());
        $this->assertSame('save', $statusLogEntity->getProcessingType());
    }

    /**
     * @return void
     */
    public function testSaveOrderPaymentHasAddressData()
    {
        $checkoutResponseTransfer = $this->createCheckoutResponse();
        $quoteTransfer = $this->getQuoteTransfer($checkoutResponseTransfer);
        $orderManager = new Saver($this->getKlarnaBusinessFactory());

        $orderManager->saveOrderPayment($quoteTransfer, $checkoutResponseTransfer);

        $paymentTransfer = $quoteTransfer->getPayment()->getKlarna();
        $addressTransfer = $paymentTransfer->getAddress();
        /** @var \Orm\Zed\Klarna\Persistence\SpyPaymentKlarna $paymentEntity */
        $paymentEntity = SpyPaymentKlarnaQuery::create()->findOneByFkSalesOrder($checkoutResponseTransfer->getSaveOrder()->getIdSalesOrder());
        $this->assertEquals($addressTransfer->getCity(), $paymentEntity->getCity());
        $this->assertEquals($addressTransfer->getIso2Code(), $paymentEntity->getCountryIso2Code());
        $this->assertEquals($addressTransfer->getZipCode(), $paymentEntity->getZipCode());
        $this->assertEquals($addressTransfer->getEmail(), $paymentEntity->getEmail());
        $this->assertEquals($addressTransfer->getFirstName(), $paymentEntity->getFirstName());
        $this->assertEquals($addressTransfer->getLastName(), $paymentEntity->getLastName());
        $this->assertEquals($addressTransfer->getSalutation(), $paymentEntity->getSalutation());
        $this->assertEquals($addressTransfer->getPhone(), $paymentEntity->getPhone());
        $this->assertEquals($addressTransfer->getCellPhone(), $paymentEntity->getCellPhone());
        $this->assertEquals(
            trim(
                sprintf(
                    '%s %s %s',
                    $addressTransfer->getAddress1(),
                    $addressTransfer->getAddress2(),
                    $addressTransfer->getAddress3()
                )
            ),
            $paymentEntity->getStreet()
        );
    }

    /**
     * @return \SprykerEco\Zed\Klarna\Business\KlarnaBusinessFactory
     */
    private function getKlarnaBusinessFactory()
    {
        $businessFactory = new KlarnaBusinessFactory();

        return $businessFactory;
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
            ->setAccountBrand(KlarnaConstants::BRAND_INVOICE)
            ->setLanguageIso2Code('de')
            ->setCurrencyIso3Code('EUR')
            ->setAddress($paymentAddressTransfer);

        $quoteTransfer = new QuoteTransfer();

        $customerTransfer = new CustomerTransfer();
        $customerTransfer->setEmail($email);
        $customerTransfer->setIsGuest(true);
        $quoteTransfer->setCustomer($customerTransfer);

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
        $bundleEntity = $this->createOrderItemBundleEntity();

        $orderItemEntity = new SpySalesOrderItem();
        $orderItemEntity
            ->setFkSalesOrder($idSalesOrder)
            ->setFkOmsOrderItemState($stateEntity->getIdOmsOrderItemState())
            ->setFkOmsOrderProcess($processEntity->getIdOmsOrderProcess())
            ->setFkSalesOrderItemBundle($bundleEntity->getIdSalesOrderItemBundle())
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
     * @return \Orm\Zed\Sales\Persistence\SpySalesOrderItemBundle
     */
    private function createOrderItemBundleEntity()
    {
        $bundleEntity = new SpySalesOrderItemBundle();
        $bundleEntity
            ->setName('test bundle')
            ->setSku('13243546')
            ->setGrossPrice(1000)
            ->setBundleType('NonSplitBundle');
        $bundleEntity->save();

        $bundleItemEntity = new SpySalesOrderItemBundleItem();
        $bundleItemEntity
            ->setFkSalesOrderItemBundle($bundleEntity->getIdSalesOrderItemBundle())
            ->setName('test bundle item')
            ->setSku('13243546')
            ->setGrossPrice(1000)
            ->setVariety('Simple');
        $bundleItemEntity->save();

        return $bundleEntity;
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

}
