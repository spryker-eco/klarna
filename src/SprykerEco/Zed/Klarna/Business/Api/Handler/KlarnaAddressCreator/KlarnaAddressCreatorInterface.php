<?php
/**
 * MIT License
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */
namespace SprykerEco\Zed\Klarna\Business\Api\Handler\KlarnaAddressCreator;

use Generated\Shared\Transfer\AddressTransfer;

interface KlarnaAddressCreatorInterface
{

    /**
     * @param \Generated\Shared\Transfer\AddressTransfer $addressTransfer
     *
     * @return \KlarnaAddr
     */
    public function createKlarnaAddress(AddressTransfer $addressTransfer);

}
