<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace SprykerEco\Zed\Klarna\Business;

use Generated\Shared\Transfer\CheckoutResponseTransfer;
use Generated\Shared\Transfer\KlarnaCheckoutServiceRequestTransfer;
use Generated\Shared\Transfer\KlarnaCheckoutTransfer;
use Generated\Shared\Transfer\KlarnaGetAddressesRequestTransfer;
use Generated\Shared\Transfer\OrderTransfer;
use Generated\Shared\Transfer\QuoteTransfer;
use Orm\Zed\Klarna\Persistence\SpyPaymentKlarna;
use Propel\Runtime\Collection\ObjectCollection;
use Spryker\Zed\Kernel\Business\AbstractFacade;

/**
 * Class KlarnaFacade
 *
 * @package SprykerEco\Zed\Klarna\Business
 *
 * @author Daniel Bohnhardt <daniel.bohnhardt@twt.de>
 * @method \SprykerEco\Zed\Klarna\Business\KlarnaBusinessFactory getFactory()
 */
class KlarnaFacade extends AbstractFacade
{

    /**
     * @api
     * @param \Generated\Shared\Transfer\QuoteTransfer $quoteTransfer
     * @param \Generated\Shared\Transfer\CheckoutResponseTransfer $checkoutResponseTransfer
     *
     * @return void
     * @author Daniel Bohnhardt <daniel.bohnhardt@twt.de>
     */
    public function saveOrderPayment(QuoteTransfer $quoteTransfer, CheckoutResponseTransfer $checkoutResponseTransfer)
    {
        $this->getFactory()
             ->createOrderSaver()
             ->saveOrderPayment($quoteTransfer, $checkoutResponseTransfer);
    }

    /**
     * @api
     * @param \Generated\Shared\Transfer\QuoteTransfer $quoteTransfer
     *
     * @return \Generated\Shared\Transfer\KlarnaReserveAmountResponseTransfer
     * @author Daniel Bohnhardt <daniel.bohnhardt@twt.de>
     */
    public function reserveAmount(QuoteTransfer $quoteTransfer)
    {
        return $this->getFactory()->createReserveAmount()->reserveAmount($quoteTransfer);
    }

    /**
     * @api
     * @param \Generated\Shared\Transfer\QuoteTransfer $quoteTransfer
     *
     * @return \Generated\Shared\Transfer\KlarnaReserveAmountResponseTransfer
     * @author Daniel Bohnhardt <daniel.bohnhardt@twt.de>
     */
    public function updatePayment(QuoteTransfer $quoteTransfer)
    {
        return $this->getFactory()->createReserveAmount()->updateReservation($quoteTransfer);
    }

    /**
     * @api
     * @param \Orm\Zed\Klarna\Persistence\SpyPaymentKlarna $paymentKlarna
     * @param \Generated\Shared\Transfer\OrderTransfer $orderTransfer
     *
     * @return array
     * @author Daniel Bohnhardt <daniel.bohnhardt@twt.de>
     */
    public function capturePayment(SpyPaymentKlarna $paymentKlarna, OrderTransfer $orderTransfer)
    {
        return $this->getFactory()->createKlarnaApi()->activateOrder($paymentKlarna, $orderTransfer);
    }

    /**
     * @api
     * @param \Orm\Zed\Klarna\Persistence\SpyPaymentKlarna $paymentKlarna
     *
     * @return string
     * @author Daniel Bohnhardt <daniel.bohnhardt@twt.de>
     */
    public function refundPayment(SpyPaymentKlarna $paymentKlarna)
    {
        return $this->getFactory()->createKlarnaApi()->creditInvoice($paymentKlarna);
    }

    /**
     * @api
     * @param \Orm\Zed\Klarna\Persistence\SpyPaymentKlarna $paymentKlarna
     *
     * @return string
     * @author Daniel Bohnhardt <daniel.bohnhardt@twt.de>
     */
    public function sendInvoiceByMail(SpyPaymentKlarna $paymentKlarna)
    {
        return $this->getFactory()->createKlarnaApi()->sendInvoiceByMail($paymentKlarna);
    }

    /**
     * @api
     * @param \Orm\Zed\Klarna\Persistence\SpyPaymentKlarna $paymentKlarna
     *
     * @return string
     * @author Daniel Bohnhardt <daniel.bohnhardt@twt.de>
     */
    public function sendInvoiceByPost(SpyPaymentKlarna $paymentKlarna)
    {
        return $this->getFactory()->createKlarnaApi()->sendInvoiceByPost($paymentKlarna);
    }

    /**
     * @api
     * @param \Generated\Shared\Transfer\QuoteTransfer $quoteTransfer
     *
     * @return \Generated\Shared\Transfer\KlarnaInstallmentResponseTransfer
     * @author Daniel Bohnhardt <daniel.bohnhardt@twt.de>
     */
    public function getInstallments(QuoteTransfer $quoteTransfer)
    {
        return $this->getFactory()->createInstallment()->getInstallments($quoteTransfer);
    }

    /**
     * @api
     * @param \Propel\Runtime\Collection\ObjectCollection $orders
     *
     * @return array
     * @author Daniel Bohnhardt <daniel.bohnhardt@twt.de>
     */
    public function getPaymentLogs(ObjectCollection $orders)
    {
        return $this->getFactory()->createPaymentLog()->getPaymentLogs($orders);
    }

    /**
     * @api
     * @param \Generated\Shared\Transfer\QuoteTransfer $quoteTransfer
     *
     * @return \Generated\Shared\Transfer\KlarnaCheckoutTransfer
     * @author Daniel Bohnhardt <daniel.bohnhardt@twt.de>
     */
    public function getKlarnaCheckoutHtml(QuoteTransfer $quoteTransfer)
    {
        return $this->getFactory()->createKlarnaCheckout()->getCheckoutHtml($quoteTransfer);
    }

    /**
     * @api
     * @param \Generated\Shared\Transfer\KlarnaCheckoutTransfer $klarnaCheckoutTransfer
     *
     * @return \Generated\Shared\Transfer\KlarnaCheckoutTransfer
     * @author Daniel Bohnhardt <daniel.bohnhardt@twt.de>
     */
    public function getKlarnaSuccessHtml(KlarnaCheckoutTransfer $klarnaCheckoutTransfer)
    {
        return $this->getFactory()->createKlarnaCheckout()->getSuccessHtml($klarnaCheckoutTransfer);
    }

    /**
     * @api
     * @param \Generated\Shared\Transfer\KlarnaCheckoutTransfer $klarnaCheckoutTransfer
     *
     * @return bool
     * @author Daniel Bohnhardt <daniel.bohnhardt@twt.de>
     */
    public function createCheckoutOrder(KlarnaCheckoutTransfer $klarnaCheckoutTransfer)
    {
        return $this->getFactory()->createKlarnaCheckout()->createCheckoutOrder($klarnaCheckoutTransfer);
    }

    /**
     * @api
     * @param array $orderItems
     * @param \Orm\Zed\Klarna\Persistence\SpyPaymentKlarna $paymentKlarna
     * @param \Generated\Shared\Transfer\OrderTransfer $orderTransfer
     *
     * @return array
     * @author Daniel Bohnhardt <daniel.bohnhardt@twt.de>
     */
    public function capturePartPayment(array $orderItems, SpyPaymentKlarna $paymentKlarna, OrderTransfer $orderTransfer)
    {
        return $this->getFactory()->createKlarnaApi()->activatePartOrder($orderItems, $paymentKlarna, $orderTransfer);
    }

    /**
     * @api
     * @param \Orm\Zed\Klarna\Persistence\SpyPaymentKlarna $paymentKlarna
     *
     * @return void
     * @throws \KlarnaException
     */
    public function ship(SpyPaymentKlarna $paymentKlarna)
    {
        $this->getFactory()->createKlarnaApi()->ship($paymentKlarna);
    }

    /**
     * @api
     * @param array $orderItems
     * @param \Orm\Zed\Klarna\Persistence\SpyPaymentKlarna $paymentKlarna
     *
     * @return string
     * @throws \KlarnaException
     * @author Daniel Bohnhardt <daniel.bohnhardt@twt.de>
     */
    public function refundPartPayment(array $orderItems, SpyPaymentKlarna $paymentKlarna)
    {
        return $this->getFactory()->createKlarnaApi()->creditPart($orderItems, $paymentKlarna);
    }

    /**
     * @api
     * @param \Orm\Zed\Klarna\Persistence\SpyPaymentKlarna $paymentEntity
     *
     * @return int
     * @author Daniel Bohnhardt <daniel.bohnhardt@twt.de>
     */
    public function checkOrderStatus(SpyPaymentKlarna $paymentEntity)
    {
        return $this->getFactory()->createKlarnaApi()->checkOrderStatus($paymentEntity);
    }

    /**
     * @api
     * @param \Orm\Zed\Klarna\Persistence\SpyPaymentKlarna $paymentEntity
     *
     * @return bool
     * @author Daniel Bohnhardt <daniel.bohnhardt@twt.de>
     */
    public function cancelOrder(SpyPaymentKlarna $paymentEntity)
    {
        return $this->getFactory()->createKlarnaApi()->cancelReservation($paymentEntity);
    }

    /**
     * @api
     * @param int $salesOrderId
     *
     * @return array
     * @author Daniel Bohnhardt <daniel.bohnhardt@twt.de>
     */
    public function getKlarnaPaymentById($salesOrderId)
    {
        return $this->getFactory()->getKlarnaPaymentById($salesOrderId);
    }

    /**
     * @api
     *
     * @pai
     * @param int $salesOrderId
     *
     * @return string
     * @author Daniel Bohnhardt <daniel.bohnhardt@twt.de>
     */
    public function getInvoicePdfUrl($salesOrderId)
    {
        return $this->getFactory()->getInvoicePdfUrl($salesOrderId);
    }

    /**
     * @api
     * @param \Generated\Shared\Transfer\KlarnaCheckoutServiceRequestTransfer $klarnaCheckoutServiceRequestTransfer
     *
     * @return \Generated\Shared\Transfer\KlarnaCheckoutServiceResponseTransfer
     */
    public function checkoutService(KlarnaCheckoutServiceRequestTransfer $klarnaCheckoutServiceRequestTransfer)
    {
        return $this->getFactory()->createCheckoutServiceRequest()->getInstallments($klarnaCheckoutServiceRequestTransfer);
    }

    /**
     * @api
     *
     * @param \Generated\Shared\Transfer\KlarnaGetAddressesRequestTransfer $klarnaGetAddressesRequestTransfer
     *
     * @return \Generated\Shared\Transfer\KlarnaGetAddressesResponseTransfer
     */
    public function getAddresses(KlarnaGetAddressesRequestTransfer $klarnaGetAddressesRequestTransfer)
    {
        return $this->getFactory()->createGetAddressesRequest()->getAddresses($klarnaGetAddressesRequestTransfer);
    }

    /**
     * @api
     *
     * @param \Generated\Shared\Transfer\OrderTransfer $salesOrderTransfer
     *
     * @return \SprykerEco\Zed\Klarna\Business\Address\AddressUpdater
     */
    public function getAddressUpdater(OrderTransfer $salesOrderTransfer)
    {
        $klarnaPayment = $this->getKlarnaPaymentById($salesOrderTransfer->getIdSalesOrder());

        return $this->getFactory()->createAddressUpdater($klarnaPayment[0]);
    }

}
