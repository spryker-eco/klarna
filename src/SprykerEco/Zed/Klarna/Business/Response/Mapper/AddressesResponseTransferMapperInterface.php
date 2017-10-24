<?php

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
