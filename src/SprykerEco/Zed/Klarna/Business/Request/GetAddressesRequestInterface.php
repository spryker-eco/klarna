<?php

/**
 * MIT License
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace SprykerEco\Zed\Klarna\Business\Request;

use Generated\Shared\Transfer\KlarnaGetAddressesRequestTransfer;

interface GetAddressesRequestInterface
{
    /**
     * @param \Generated\Shared\Transfer\KlarnaGetAddressesRequestTransfer $klarnaGetAddressesRequestTransfer
     *
     * @return \Generated\Shared\Transfer\KlarnaGetAddressesResponseTransfer
     */
    public function getAddresses(KlarnaGetAddressesRequestTransfer $klarnaGetAddressesRequestTransfer);
}
