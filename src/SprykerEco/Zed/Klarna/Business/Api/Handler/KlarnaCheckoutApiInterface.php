<?php

namespace SprykerEco\Zed\Klarna\Business\Api\Handler;

use Generated\Shared\Transfer\KlarnaCheckoutTransfer;
use Generated\Shared\Transfer\QuoteTransfer;

interface KlarnaCheckoutApiInterface
{

    /**
     * @param \Generated\Shared\Transfer\QuoteTransfer $quoteTransfer
     *
     * @throws \SprykerEco\Zed\Klarna\Business\Exception\NoShippingException
     *
     * @return array
     */
    public function getCheckoutValues(QuoteTransfer $quoteTransfer);

    /**
     * @param \Generated\Shared\Transfer\KlarnaCheckoutTransfer $klarnaCheckoutTransfer
     *
     * @return array
     */
    public function getSuccessValues(KlarnaCheckoutTransfer $klarnaCheckoutTransfer);

    /**
     * Mark checkout order as created.
     *
     * @param \Generated\Shared\Transfer\KlarnaCheckoutTransfer $klarnaCheckoutTransfer
     *
     * @return void
     */
    public function createOrder(KlarnaCheckoutTransfer $klarnaCheckoutTransfer);

    /**
     * @param \Generated\Shared\Transfer\KlarnaCheckoutTransfer $klarnaCheckoutTransfer
     *
     * @return \Klarna_Checkout_Order
     */
    public function fetchKlarnaOrder(KlarnaCheckoutTransfer $klarnaCheckoutTransfer);
}
