<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Zed\Klarna\Business\Response\Mapper;

use Generated\Shared\Transfer\KlarnaAddressTransfer;
use Generated\Shared\Transfer\KlarnaGetAddressesResponseTransfer;

class AddressesResponseTransferMapper
{

    /**
     * @param array $klarnaAddresses
     *
     * @return \Generated\Shared\Transfer\KlarnaGetAddressesResponseTransfer
     */
    public function map(
        $klarnaAddresses
    ) {
        $klarnaGetAddressesResponseTransfer = new KlarnaGetAddressesResponseTransfer();

        /** @var \KlarnaAddr $klarnaAddress */
        foreach ($klarnaAddresses as $klarnaAddress) {
            $klarnaAddressTransfer = new KlarnaAddressTransfer();
            $klarnaAddressTransfer->setEmail($klarnaAddress->getEmail());
            $klarnaAddressTransfer->setTelno($klarnaAddress->getTelno());
            $klarnaAddressTransfer->setCellno($klarnaAddress->getCellno());
            $klarnaAddressTransfer->setFirstName(utf8_encode($klarnaAddress->getFirstName()));
            $klarnaAddressTransfer->setLastName(utf8_encode($klarnaAddress->getLastName()));
            $klarnaAddressTransfer->setCompanyName(utf8_encode($klarnaAddress->getCompanyName()));
            $klarnaAddressTransfer->setCareof(utf8_encode($klarnaAddress->getCareof()));
            $klarnaAddressTransfer->setStreet(utf8_encode($klarnaAddress->getStreet()));
            $klarnaAddressTransfer->setZipCode($klarnaAddress->getZipCode());
            $klarnaAddressTransfer->setCity(utf8_encode($klarnaAddress->getCity()));
            $klarnaAddressTransfer->setCountry($klarnaAddress->getCountry());
            $klarnaAddressTransfer->setHouseNumber($klarnaAddress->getHouseNumber());
            $klarnaAddressTransfer->setHouseExt(utf8_encode($klarnaAddress->getHouseExt()));

            $klarnaGetAddressesResponseTransfer->addKlarnaAddress($klarnaAddressTransfer);
        }

        return $klarnaGetAddressesResponseTransfer;
    }

}
