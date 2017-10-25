<?php

/**
 * MIT License
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace SprykerEco\Zed\Klarna\Business\Response\Mapper;

interface AddressesResponseTransferMapperInterface
{
    /**
     * @param \KlarnaAddr[] $klarnaAddresses
     *
     * @return \Generated\Shared\Transfer\KlarnaGetAddressesResponseTransfer
     */
    public function map(array $klarnaAddresses);
}
