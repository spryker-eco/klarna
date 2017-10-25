<?php

/**
 * MIT License
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace SprykerEco\Zed\Klarna\Dependency\Facade;

use Generated\Shared\Transfer\QuoteTransfer;

class KlarnaToCheckoutBridge implements KlarnaToCheckoutBridgeInterface
{
    /**
     * @var \Spryker\Zed\Checkout\Business\CheckoutFacadeInterface
     */
    protected $checkoutFacade;

    /**
     * @param \Spryker\Zed\Checkout\Business\CheckoutFacadeInterface $checkoutFacade
     */
    public function __construct($checkoutFacade)
    {
        $this->checkoutFacade = $checkoutFacade;
    }

    /**
     * @param \Generated\Shared\Transfer\QuoteTransfer $quoteTransfer
     *
     * @return \Generated\Shared\Transfer\CheckoutResponseTransfer
     */
    public function placeOrder(QuoteTransfer $quoteTransfer)
    {
        return $this->checkoutFacade->placeOrder($quoteTransfer);
    }
}
