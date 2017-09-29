<?php
/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */
namespace Spryker\Zed\Klarna\Business\Api\Handler\KlarnaAddressCreator;

use Generated\Shared\Transfer\AddressTransfer;

class NetherlandKlarnaAddressCreator implements KlarnaAddressCreatorInterface
{

    /**
     * @param \Generated\Shared\Transfer\AddressTransfer $addressTransfer
     *
     * @return \KlarnaAddr
     */
    public function createKlarnaAddress(AddressTransfer $addressTransfer)
    {
        return new \KlarnaAddr(
            $addressTransfer->getEmail(), // Email address
            $addressTransfer->getPhone(), // Telephone number, only one phone number is needed
            $addressTransfer->getCellPhone(), // Cell phone number
            utf8_decode($addressTransfer->getFirstName()), // First name (given name)
            utf8_decode($addressTransfer->getLastName()), // Last name (family name)
            '', // No care of, C/O
            utf8_decode($addressTransfer->getAddress1()), // Street address
            $addressTransfer->getZipCode(), // Zip code
            utf8_decode($addressTransfer->getCity()), // City
            \KlarnaCountry::fromCode($addressTransfer->getIso2Code()), // Country
            $addressTransfer->getAddress2(), // House number (AT/DE/NL only)
            $addressTransfer->getAddress3() // House extension (NL only)
        );
    }

}
