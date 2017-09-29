<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Zed\Klarna\Dependency\Facade;

use Generated\Shared\Transfer\QuoteTransfer;

/**
 * Class KlarnaToCheckoutBridge
 *
 * @package Spryker\Zed\Klarna\Dependency\Facade
 *
 * @author Daniel Bohnhardt <daniel.bohnhardt@twt.de>
 */
interface KlarnaToCheckoutBridgeInterface
{

    /**
     * @param \Generated\Shared\Transfer\QuoteTransfer $quoteTransfer
     *
     * @return \Generated\Shared\Transfer\CheckoutResponseTransfer
     * @author Daniel Bohnhardt <daniel.bohnhardt@twt.de>
     */
    public function placeOrder(QuoteTransfer $quoteTransfer);

}
