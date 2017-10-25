<?php

/**
 * MIT License
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace SprykerEco\Client\Klarna;

use Generated\Shared\Transfer\KlarnaCheckoutServiceRequestTransfer;
use Generated\Shared\Transfer\KlarnaCheckoutTransfer;
use Generated\Shared\Transfer\KlarnaGetAddressesRequestTransfer;
use Generated\Shared\Transfer\KlarnaInstallmentResponseTransfer;
use Generated\Shared\Transfer\QuoteTransfer;

/**
 * Class KlarnaClient
 *
 * @package SprykerEco\Client\Klarna
 *
 * @author Daniel Bohnhardt <daniel.bohnhardt@twt.de>
 * @method \SprykerEco\Client\Klarna\KlarnaFactory getFactory()
 */
interface KlarnaClientInterface
{
    /**
     * Specification:
     * - update payment
     *
     * @api
     *
     * @param \Generated\Shared\Transfer\QuoteTransfer $quoteTransfer
     *
     * @return \Generated\Shared\Transfer\KlarnaReserveAmountResponseTransfer
     */
    public function updatePayment(QuoteTransfer $quoteTransfer);

    /**
     * Specification:
     * - get installments from quote
     *
     * @api
     *
     * @param \Generated\Shared\Transfer\QuoteTransfer $quoteTransfer
     *
     * @return \Generated\Shared\Transfer\KlarnaInstallmentResponseTransfer
     */
    public function getInstallments(QuoteTransfer $quoteTransfer);

    /**
     * Specification:
     * - check is there are installments in session
     *
     * @api
     *
     * @return bool
     */
    public function hasInstallmentsInSession();

    /**
     * Specification:
     * - store installments into session
     *
     * @api
     *
     * @param \Generated\Shared\Transfer\KlarnaInstallmentResponseTransfer $installmentResponseTransfer
     *
     * @return \Generated\Shared\Transfer\KlarnaInstallmentResponseTransfer
     */
    public function storeInstallmentsInSession(KlarnaInstallmentResponseTransfer $installmentResponseTransfer);

    /**
     * Specification:
     * - remove installments
     *
     * @api
     *
     * @return bool
     */
    public function removeInstallmentsFromSession();

    /**
     * Specification:
     * - get installments from session
     *
     * @api
     *
     * @return \Generated\Shared\Transfer\KlarnaInstallmentResponseTransfer
     */
    public function getInstallmentsFromSession();

    /**
     * Specification:
     * - get checkout HTML
     *
     * @api
     *
     * @param \Generated\Shared\Transfer\QuoteTransfer $quoteTransfer
     *
     * @return \Spryker\Shared\Kernel\Transfer\TransferInterface
     */
    public function getKlarnaCheckoutHtml(QuoteTransfer $quoteTransfer);

    /**
     * Specification:
     * - store Klarna order id in session
     *
     * @api
     *
     * @param string $orderId
     *
     * @return void
     */
    public function storeKlarnaOrderIdInSession($orderId);

    /**
     * Specification:
     * - get Klarna id from session
     *
     * @api
     *
     * @return string
     */
    public function getKlarnaOrderIdFromSession();

    /**
     * Specification:
     * - remove Klarna order id from session
     *
     * @api
     *
     * @return bool
     */
    public function removeKlarnaOrderIdFromSession();

    /**
     * Specification:
     * - returns true if there's Klarna order id in session
     *
     * @api
     *
     * @return bool
     */
    public function hasKlarnaOrderIdInSession();

    /**
     * Specification:
     * - renders success HTML transfer object
     *
     * @api
     *
     * @param \Generated\Shared\Transfer\KlarnaCheckoutTransfer $klarnaCheckoutTransfer
     *
     * @return \Generated\Shared\Transfer\KlarnaCheckoutTransfer
     */
    public function getSuccessHtml(KlarnaCheckoutTransfer $klarnaCheckoutTransfer);

    /**
     * Specification:
     * - create checkout order from Klarna checkout data
     *
     * @api
     *
     * @param \Generated\Shared\Transfer\KlarnaCheckoutTransfer $klarnaCheckoutTransfer
     *
     * @return \Spryker\Shared\Kernel\Transfer\TransferInterface
     */
    public function createCheckoutOrder(KlarnaCheckoutTransfer $klarnaCheckoutTransfer);

    /**
     * Specification:
     * - query Klarna service
     *
     * @api
     *
     * @param \Generated\Shared\Transfer\KlarnaCheckoutServiceRequestTransfer $klarnaCheckoutServiceRequestTransfer
     *
     * @return \Generated\Shared\Transfer\KlarnaCheckoutServiceResponseTransfer
     */
    public function checkoutService(KlarnaCheckoutServiceRequestTransfer $klarnaCheckoutServiceRequestTransfer);

    /**
     * Specification:
     * - query Klarna service for address
     *
     * @api
     *
     * @param \Generated\Shared\Transfer\KlarnaGetAddressesRequestTransfer $klarnaGetAddressesRequestTransfer
     *
     * @return \Generated\Shared\Transfer\KlarnaGetAddressesResponseTransfer
     */
    public function getAddresses(KlarnaGetAddressesRequestTransfer $klarnaGetAddressesRequestTransfer);
}
