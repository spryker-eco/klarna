<?php

/**
 * MIT License
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace SprykerEco\Zed\Klarna\Business\Request;

use Generated\Shared\Transfer\KlarnaGetAddressesRequestTransfer;
use SprykerEco\Zed\Klarna\Business\Api\Handler\KlarnaApiInterface;
use SprykerEco\Zed\Klarna\Business\Response\Mapper\AddressesResponseTransferMapperInterface;

class GetAddressesRequest implements GetAddressesRequestInterface
{
    /**
     * @var \SprykerEco\Zed\Klarna\Business\Api\Handler\KlarnaApiInterface
     */
    protected $klarnaApi;

    /**
     * @var \SprykerEco\Zed\Klarna\Business\Response\Mapper\AddressesResponseTransferMapperInterface
     */
    protected $addressesResponseTransferMapper;

    /**
     * @author Daniel Bohnhardt <daniel.bohnhardt@twt.de>
     *
     * @param \SprykerEco\Zed\Klarna\Business\Api\Handler\KlarnaApiInterface $klarnaApi
     * @param \SprykerEco\Zed\Klarna\Business\Response\Mapper\AddressesResponseTransferMapperInterface $addressesResponseTransferMapper
     */
    public function __construct(
        KlarnaApiInterface $klarnaApi,
        AddressesResponseTransferMapperInterface $addressesResponseTransferMapper
    ) {
        $this->klarnaApi = $klarnaApi;
        $this->addressesResponseTransferMapper = $addressesResponseTransferMapper;
    }

    /**
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
