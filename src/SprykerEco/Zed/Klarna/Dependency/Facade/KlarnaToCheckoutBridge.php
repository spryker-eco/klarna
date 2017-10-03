<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace SprykerEco\Zed\Klarna\Dependency\Facade;

use Generated\Shared\Transfer\QuoteTransfer;
use Spryker\Zed\Checkout\Business\CheckoutFacadeInterface;

/**
 * Class KlarnaToCheckoutBridge
 *
 * @package SprykerEco\Zed\Klarna\Dependency\Facade
 *
 * @author Daniel Bohnhardt <daniel.bohnhardt@twt.de>
 */
class KlarnaToCheckoutBridge implements KlarnaToCheckoutBridgeInterface
{

    /**
     * @var \Spryker\Zed\Checkout\Business\CheckoutFacadeInterface
     *
     * @author Daniel Bohnhardt <daniel.bohnhardt@twt.de>
     */
    protected $checkoutFacade;

    /**
     * KlarnaToCheckoutBridge constructor.
     *
     * @author Daniel Bohnhardt <daniel.bohnhardt@twt.de>
     *
     * @param \Spryker\Zed\Checkout\Business\CheckoutFacadeInterface $checkoutFacade
     */
    public function __construct(CheckoutFacadeInterface $checkoutFacade)
    {
        $this->checkoutFacade = $checkoutFacade;
    }

    /**
     * @author Daniel Bohnhardt <daniel.bohnhardt@twt.de>
     *
     * @param \Generated\Shared\Transfer\QuoteTransfer $quoteTransfer
     *
     * @return \Generated\Shared\Transfer\CheckoutResponseTransfer
     */
    public function placeOrder(QuoteTransfer $quoteTransfer)
    {
        return $this->checkoutFacade->placeOrder($quoteTransfer);
    }

}
