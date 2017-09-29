<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Client\Klarna;

use Generated\Shared\Transfer\KlarnaCheckoutServiceRequestTransfer;
use Generated\Shared\Transfer\KlarnaCheckoutTransfer;
use Generated\Shared\Transfer\KlarnaGetAddressesRequestTransfer;
use Generated\Shared\Transfer\KlarnaInstallmentResponseTransfer;
use Generated\Shared\Transfer\QuoteTransfer;

/**
 * Class KlarnaClient
 *
 * @package Spryker\Client\Klarna
 *
 * @author Daniel Bohnhardt <daniel.bohnhardt@twt.de>
 * @method \Spryker\Client\Klarna\KlarnaFactory getFactory()
 */
interface KlarnaClientInterface
{

    /**
     * @api
     * @param \Generated\Shared\Transfer\QuoteTransfer $quoteTransfer
     *
     * @return \Generated\Shared\Transfer\KlarnaReserveAmountResponseTransfer
     * @author Daniel Bohnhardt <daniel.bohnhardt@twt.de>
     */
    public function updatePayment(QuoteTransfer $quoteTransfer);

    /**
     * @api
     * @param \Generated\Shared\Transfer\QuoteTransfer $quoteTransfer
     *
     * @return \Generated\Shared\Transfer\KlarnaInstallmentResponseTransfer
     * @author Daniel Bohnhardt <daniel.bohnhardt@twt.de>
     */
    public function getInstallments(QuoteTransfer $quoteTransfer);

    /**
     * @api
     * @return \Spryker\Client\Klarna\Session\KlarnaSession
     * @author Daniel Bohnhardt <daniel.bohnhardt@twt.de>
     */
    public function getSession();

    /**
     * @api
     * @return bool
     * @author Daniel Bohnhardt <daniel.bohnhardt@twt.de>
     */
    public function hasInstallmentsInSession();

    /**
     * @api
     * @param \Generated\Shared\Transfer\KlarnaInstallmentResponseTransfer $installmentResponseTransfer
     *
     * @return \Generated\Shared\Transfer\KlarnaInstallmentResponseTransfer
     * @author Daniel Bohnhardt <daniel.bohnhardt@twt.de>
     */
    public function storeInstallmentsInSession(KlarnaInstallmentResponseTransfer $installmentResponseTransfer);

    /**
     * @api
     * @return bool
     * @author Daniel Bohnhardt <daniel.bohnhardt@twt.de>
     */
    public function removeInstallmentsFromSession();

    /**
     * @api
     * @return \Generated\Shared\Transfer\KlarnaInstallmentResponseTransfer
     * @author Daniel Bohnhardt <daniel.bohnhardt@twt.de>
     */
    public function getInstallmentsFromSession();

    /**
     * @api
     * @param \Generated\Shared\Transfer\QuoteTransfer $quoteTransfer
     *
     * @return \Spryker\Shared\Transfer\TransferInterface
     * @author Daniel Bohnhardt <daniel.bohnhardt@twt.de>
     */
    public function getKlarnaCheckoutHtml(QuoteTransfer $quoteTransfer);

    /**
     * @api
     * @param string $orderId
     *
     * @return void
     * @author Daniel Bohnhardt <daniel.bohnhardt@twt.de>
     */
    public function storeKlarnaOrderIdInSession($orderId);

    /**
     * @api
     * @return string
     * @author Daniel Bohnhardt <daniel.bohnhardt@twt.de>
     */
    public function getKlarnaOrderIdFromSession();

    /**
     * @api
     * @return bool
     * @author Daniel Bohnhardt <daniel.bohnhardt@twt.de>
     */
    public function removeKlarnaOrderIdFromSession();

    /**
     * @api
     * @return bool
     * @author Daniel Bohnhardt <daniel.bohnhardt@twt.de>
     */
    public function hasKlarnaOrderIdInSession();

    /**
     * @api
     * @param \Generated\Shared\Transfer\KlarnaCheckoutTransfer $klarnaCheckoutTransfer
     *
     * @return \Generated\Shared\Transfer\KlarnaCheckoutTransfer
     * @author Daniel Bohnhardt <daniel.bohnhardt@twt.de>
     */
    public function getSuccessHtml(KlarnaCheckoutTransfer $klarnaCheckoutTransfer);

    /**
     * @api
     * @param \Generated\Shared\Transfer\KlarnaCheckoutTransfer $klarnaCheckoutTransfer
     *
     * @return mixed
     * @author Daniel Bohnhardt <daniel.bohnhardt@twt.de>
     */
    public function createCheckoutOrder(KlarnaCheckoutTransfer $klarnaCheckoutTransfer);

    /**
     * @api
     * @param \Generated\Shared\Transfer\KlarnaCheckoutServiceRequestTransfer $klarnaCheckoutServiceRequestTransfer
     *
     * @return \Generated\Shared\Transfer\KlarnaCheckoutServiceResponseTransfer
     */
    public function checkoutService(KlarnaCheckoutServiceRequestTransfer $klarnaCheckoutServiceRequestTransfer);

    /**
     * @api
     * @param \Generated\Shared\Transfer\KlarnaGetAddressesRequestTransfer $klarnaGetAddressesRequestTransfer
     *
     * @return \Generated\Shared\Transfer\KlarnaGetAddressesResponseTransfer
     */
    public function getAddresses(KlarnaGetAddressesRequestTransfer $klarnaGetAddressesRequestTransfer);

}
