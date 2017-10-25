<?php

/**
 * MIT License
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

interface KlarnaFacadeInterface
{
    /**
     * @api
     *
     * @param \Generated\Shared\Transfer\QuoteTransfer $quoteTransfer
     * @param \Generated\Shared\Transfer\CheckoutResponseTransfer $checkoutResponseTransfer
     *
     * @return void
     */
    public function saveOrderPayment(QuoteTransfer $quoteTransfer, CheckoutResponseTransfer $checkoutResponseTransfer);

    /**
     * @api
     *
     * @param \Generated\Shared\Transfer\QuoteTransfer $quoteTransfer
     *
     * @return \Generated\Shared\Transfer\KlarnaReserveAmountResponseTransfer
     */
    public function reserveAmount(QuoteTransfer $quoteTransfer);

    /**
     * @api
     *
     * @param \Generated\Shared\Transfer\QuoteTransfer $quoteTransfer
     *
     * @return \Generated\Shared\Transfer\KlarnaReserveAmountResponseTransfer
     */
    public function updatePayment(QuoteTransfer $quoteTransfer);

    /**
     * @api
     *
     * @param \Orm\Zed\Klarna\Persistence\SpyPaymentKlarna $paymentKlarna
     * @param \Generated\Shared\Transfer\OrderTransfer $orderTransfer
     *
     * @return array
     */
    public function capturePayment(SpyPaymentKlarna $paymentKlarna, OrderTransfer $orderTransfer);

    /**
     * @api
     *
     * @param \Orm\Zed\Klarna\Persistence\SpyPaymentKlarna $paymentKlarna
     *
     * @return string
     */
    public function refundPayment(SpyPaymentKlarna $paymentKlarna);

    /**
     * @api
     *
     * @param \Orm\Zed\Klarna\Persistence\SpyPaymentKlarna $paymentKlarna
     *
     * @return string
     */
    public function sendInvoiceByMail(SpyPaymentKlarna $paymentKlarna);

    /**
     * @api
     *
     * @param \Orm\Zed\Klarna\Persistence\SpyPaymentKlarna $paymentKlarna
     *
     * @return string
     */
    public function sendInvoiceByPost(SpyPaymentKlarna $paymentKlarna);

    /**
     * @api
     *
     * @param \Generated\Shared\Transfer\QuoteTransfer $quoteTransfer
     *
     * @return \Generated\Shared\Transfer\KlarnaInstallmentResponseTransfer
     */
    public function getInstallments(QuoteTransfer $quoteTransfer);

    /**
     * @api
     *
     * @param \Propel\Runtime\Collection\ObjectCollection $orders
     *
     * @return array
     */
    public function getPaymentLogs(ObjectCollection $orders);

    /**
     * @api
     *
     * @param \Generated\Shared\Transfer\QuoteTransfer $quoteTransfer
     *
     * @return \Generated\Shared\Transfer\KlarnaCheckoutTransfer
     */
    public function getKlarnaCheckoutHtml(QuoteTransfer $quoteTransfer);

    /**
     * @api
     *
     * @param \Generated\Shared\Transfer\KlarnaCheckoutTransfer $klarnaCheckoutTransfer
     *
     * @return \Generated\Shared\Transfer\KlarnaCheckoutTransfer
     */
    public function getKlarnaSuccessHtml(KlarnaCheckoutTransfer $klarnaCheckoutTransfer);

    /**
     * @api
     *
     * @param \Generated\Shared\Transfer\KlarnaCheckoutTransfer $klarnaCheckoutTransfer
     *
     * @return bool
     */
    public function createCheckoutOrder(KlarnaCheckoutTransfer $klarnaCheckoutTransfer);

    /**
     * @api
     *
     * @param array $orderItems
     * @param \Orm\Zed\Klarna\Persistence\SpyPaymentKlarna $paymentKlarna
     * @param \Generated\Shared\Transfer\OrderTransfer $orderTransfer
     *
     * @return array
     */
    public function capturePartPayment(array $orderItems, SpyPaymentKlarna $paymentKlarna, OrderTransfer $orderTransfer);

    /**
     * @api
     *
     * @param \Orm\Zed\Klarna\Persistence\SpyPaymentKlarna $paymentKlarna
     *
     * @throws \KlarnaException
     *
     * @return void
     */
    public function ship(SpyPaymentKlarna $paymentKlarna);

    /**
     * @api
     *
     * @param array $orderItems
     * @param \Orm\Zed\Klarna\Persistence\SpyPaymentKlarna $paymentKlarna
     *
     * @throws \KlarnaException
     *
     * @return string
     */
    public function refundPartPayment(array $orderItems, SpyPaymentKlarna $paymentKlarna);

    /**
     * @api
     *
     * @param \Orm\Zed\Klarna\Persistence\SpyPaymentKlarna $paymentEntity
     *
     * @return int
     */
    public function checkOrderStatus(SpyPaymentKlarna $paymentEntity);

    /**
     * @api
     *
     * @param \Orm\Zed\Klarna\Persistence\SpyPaymentKlarna $paymentEntity
     *
     * @return bool
     */
    public function cancelOrder(SpyPaymentKlarna $paymentEntity);

    /**
     * @api
     *
     * @param int $salesOrderId
     *
     * @return array
     */
    public function getKlarnaPaymentById($salesOrderId);

    /**
     * @api
     *
     * @param int $salesOrderId
     *
     * @return string
     */
    public function getInvoicePdfUrl($salesOrderId);

    /**
     * @api
     *
     * @param \Generated\Shared\Transfer\KlarnaCheckoutServiceRequestTransfer $klarnaCheckoutServiceRequestTransfer
     *
     * @return \Generated\Shared\Transfer\KlarnaCheckoutServiceResponseTransfer
     */
    public function checkoutService(KlarnaCheckoutServiceRequestTransfer $klarnaCheckoutServiceRequestTransfer);

    /**
     * @api
     *
     * @param \Generated\Shared\Transfer\KlarnaGetAddressesRequestTransfer $klarnaGetAddressesRequestTransfer
     *
     * @return \Generated\Shared\Transfer\KlarnaGetAddressesResponseTransfer
     */
    public function getAddresses(KlarnaGetAddressesRequestTransfer $klarnaGetAddressesRequestTransfer);
}
