<?php

/**
 * MIT License
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace SprykerEco\Zed\Klarna\Business\Api\Handler;

use ArrayObject;
use Generated\Shared\Transfer\AddressTransfer;
use Generated\Shared\Transfer\KlarnaCheckoutServiceRequestTransfer;
use Generated\Shared\Transfer\KlarnaGetAddressesRequestTransfer;
use Generated\Shared\Transfer\KlarnaPaymentTransfer;
use Generated\Shared\Transfer\KlarnaPClassRequestTransfer;
use Generated\Shared\Transfer\OrderTransfer;
use Generated\Shared\Transfer\QuoteTransfer;
use Orm\Zed\Klarna\Persistence\SpyPaymentKlarna;

/**
 * Class KlarnaApi
 *
 * @package SprykerEco\Zed\Klarna\Business\Api
 *
 * @author Daniel Bohnhardt <daniel.bohnhardt@twt.de>
 */
interface KlarnaApiInterface
{
    /**
     * Activate Invoice at klarna.
     *
     * @author Daniel Bohnhardt <daniel.bohnhardt@twt.de>
     *
     * @param \Orm\Zed\Klarna\Persistence\SpyPaymentKlarna $paymentEntity
     * @param \Generated\Shared\Transfer\OrderTransfer $orderTransfer
     *
     * @return array
     */
    public function activateOrder(SpyPaymentKlarna $paymentEntity, OrderTransfer $orderTransfer);

    /**
     * Activate Invoice at klarna.
     *
     * @author Daniel Bohnhardt <daniel.bohnhardt@twt.de>
     *
     * @param array $spyOrderItems
     * @param \Orm\Zed\Klarna\Persistence\SpyPaymentKlarna $paymentEntity
     * @param \Generated\Shared\Transfer\OrderTransfer $orderTransfer
     *
     * @return array
     */
    public function activatePartOrder(array $spyOrderItems, SpyPaymentKlarna $paymentEntity, OrderTransfer $orderTransfer);

    /**
     * Get Part Payments.
     *
     * @author Daniel Bohnhardt <daniel.bohnhardt@twt.de>
     *
     * @param \Generated\Shared\Transfer\KlarnaPClassRequestTransfer $klarnaPClassRequestTransfer
     *
     * @return \KlarnaPClass[]
     */
    public function getPclasses(KlarnaPClassRequestTransfer $klarnaPClassRequestTransfer);

    /**
     * Refund Invoice.
     *
     * @author Daniel Bohnhardt <daniel.bohnhardt@twt.de>
     *
     * @param \Orm\Zed\Klarna\Persistence\SpyPaymentKlarna $paymentEntity
     *
     * @throws \KlarnaException
     *
     * @return string
     */
    public function creditInvoice(SpyPaymentKlarna $paymentEntity);

    /**
     * activate Shipment in separate call
     *
     * @param \Orm\Zed\Klarna\Persistence\SpyPaymentKlarna $paymentEntity
     *
     * @throws \KlarnaException
     *
     * @return void
     */
    public function ship(SpyPaymentKlarna $paymentEntity);

    /**
     * Refund article.
     *
     * @author Daniel Bohnhardt <daniel.bohnhardt@twt.de>
     *
     * @param array $orderItems
     * @param \Orm\Zed\Klarna\Persistence\SpyPaymentKlarna $paymentEntity
     *
     * @throws \KlarnaException
     *
     * @return string
     */
    public function creditPart(array $orderItems, SpyPaymentKlarna $paymentEntity);

    /**
     * Cancel reservation.
     *
     * @author Daniel Bohnhardt <daniel.bohnhardt@twt.de>
     *
     * @param \Orm\Zed\Klarna\Persistence\SpyPaymentKlarna $paymentEntity
     *
     * @return bool
     */
    public function cancelReservation(SpyPaymentKlarna $paymentEntity);

    /**
     * Reserve Invoice Amount.
     *
     * @author Daniel Bohnhardt <daniel.bohnhardt@twt.de>
     *
     * @param \Generated\Shared\Transfer\QuoteTransfer $quoteTransfer
     *
     * @return array
     */
    public function reserveAmount(QuoteTransfer $quoteTransfer);

    /**
     * @param \Generated\Shared\Transfer\AddressTransfer $addressTransfer
     * @param \Generated\Shared\Transfer\OrderTransfer $salesOrderTransfer
     * @param \Orm\Zed\Klarna\Persistence\SpyPaymentKlarna $spyPayment
     *
     * @return void
     */
    public function updateAddress(AddressTransfer $addressTransfer, OrderTransfer $salesOrderTransfer, SpyPaymentKlarna $spyPayment);

    /**
     * Add order items to klarna instance.
     *
     * @author Daniel Bohnhardt <daniel.bohnhardt@twt.de>
     *
     * @param \ArrayObject|\Generated\Shared\Transfer\ItemTransfer[] $items
     * @param \Klarna $klarnaApi
     *
     * @return void
     */
    public function addOrderItems(ArrayObject $items, $klarnaApi);

    /**
     * Convert addressTransfer to klarna address.
     *
     * @author Daniel Bohnhardt <daniel.bohnhardt@twt.de>
     *
     * @param \Generated\Shared\Transfer\AddressTransfer $addressTransfer
     * @param \Generated\Shared\Transfer\KlarnaPaymentTransfer|null $paymentTransfer
     *
     * @return \KlarnaAddr
     */
    public function createKlarnaAddress(AddressTransfer $addressTransfer, KlarnaPaymentTransfer $paymentTransfer = null);

    /**
     * Check order Status
     *
     * @author Daniel Bohnhardt <daniel.bohnhardt@twt.de>
     *
     * @param \Orm\Zed\Klarna\Persistence\SpyPaymentKlarna $paymentEntity
     *
     * @return int
     */
    public function checkOrderStatus(SpyPaymentKlarna $paymentEntity);

    /**
     * Update an reservation.
     *
     * @author Daniel Bohnhardt <daniel.bohnhardt@twt.de>
     *
     * @param \Generated\Shared\Transfer\QuoteTransfer $quoteTransfer
     *
     * @return string
     */
    public function update(QuoteTransfer $quoteTransfer);

    /**
     * Send invoice by email.
     *
     * @author Daniel Bohnhardt <daniel.bohnhardt@twt.de>
     *
     * @param \Orm\Zed\Klarna\Persistence\SpyPaymentKlarna $paymentEntity
     *
     * @return string
     */
    public function sendInvoiceByMail(SpyPaymentKlarna $paymentEntity);

    /**
     * Send invoice by postal service.
     *
     * @author Daniel Bohnhardt <daniel.bohnhardt@twt.de>
     *
     * @param \Orm\Zed\Klarna\Persistence\SpyPaymentKlarna $paymentEntity
     *
     * @return string
     */
    public function sendInvoiceByPost(SpyPaymentKlarna $paymentEntity);

    /**
     * Perform a checkout service request.
     *
     * @param \Generated\Shared\Transfer\KlarnaCheckoutServiceRequestTransfer $klarnaCheckoutServiceRequestTransfer
     *
     * @return \CheckoutServiceResponse
     */
    public function checkoutService(KlarnaCheckoutServiceRequestTransfer $klarnaCheckoutServiceRequestTransfer);

    /**
     * Get Addresses. Method get_addresses
     *
     * @param \Generated\Shared\Transfer\KlarnaGetAddressesRequestTransfer $klarnaGetAddressesRequestTransfer
     *
     * @return array
     */
    public function getAddresses(KlarnaGetAddressesRequestTransfer $klarnaGetAddressesRequestTransfer);
}
