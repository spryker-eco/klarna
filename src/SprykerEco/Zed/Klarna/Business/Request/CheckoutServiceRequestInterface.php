<?php

/**
 * MIT License
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace SprykerEco\Zed\Klarna\Business\Request;

use Generated\Shared\Transfer\KlarnaCheckoutServiceRequestTransfer;

interface CheckoutServiceRequestInterface
{
    /**
     * @param \Generated\Shared\Transfer\KlarnaCheckoutServiceRequestTransfer $klarnaCheckoutServiceRequestTransfer
     *
     * @return \Generated\Shared\Transfer\KlarnaCheckoutServiceResponseTransfer
     */
    public function getInstallments(KlarnaCheckoutServiceRequestTransfer $klarnaCheckoutServiceRequestTransfer);
}
