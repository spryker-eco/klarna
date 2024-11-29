<?php

/**
 * MIT License
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace SprykerEco\Zed\Klarna\Business\Request;

use Generated\Shared\Transfer\KlarnaGetAddressesRequestTransfer;
use SprykerEco\Zed\Klarna\Business\Api\Handler\KlarnaApi;
use SprykerEco\Zed\Klarna\Business\Response\Mapper\AddressesResponseTransferMapper;

class GetAddressesRequest
{

    /**
     * @var \SprykerEco\Zed\Klarna\Business\Api\Handler\KlarnaApi
     */
    protected $klarnaApi;

    /**
     * @var \SprykerEco\Zed\Klarna\Business\Response\Mapper\AddressesResponseTransferMapper
     */
    protected $addressesResponseTransferMapper;

    /**
     * @author Daniel Bohnhardt <daniel.bohnhardt@twt.de>
     *
     * @param \SprykerEco\Zed\Klarna\Business\Api\Handler\KlarnaApi $klarnaApi
     * @param \SprykerEco\Zed\Klarna\Business\Response\Mapper\AddressesResponseTransferMapper $addressesResponseTransferMapper
     */
    public function __construct(
        KlarnaApi $klarnaApi,
        AddressesResponseTransferMapper $addressesResponseTransferMapper
    ) {
        $this->klarnaApi = $klarnaApi;
        $this->addressesResponseTransferMapper = $addressesResponseTransferMapper;
    }

    /**
     * Get Addresses. Method get_addresses
     *
     * @param \Generated\Shared\Transfer\KlarnaGetAddressesRequestTransfer $klarnaGetAddressesRequestTransfer
     *
     * @return \Generated\Shared\Transfer\KlarnaGetAddressesResponseTransfer
     */
    public function getAddresses(KlarnaGetAddressesRequestTransfer $klarnaGetAddressesRequestTransfer)
    {
        $klarnaAddresses = $this->klarnaApi->getAddresses($klarnaGetAddressesRequestTransfer);

        return $this->addressesResponseTransferMapper->map($klarnaAddresses);
    }

}
