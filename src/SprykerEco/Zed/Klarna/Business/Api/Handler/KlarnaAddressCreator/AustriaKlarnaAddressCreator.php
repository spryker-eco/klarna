<?php
/**
 * MIT License
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */
namespace SprykerEco\Zed\Klarna\Business\Api\Handler\KlarnaAddressCreator;

use Generated\Shared\Transfer\AddressTransfer;
use KlarnaAddr;
use KlarnaCountry;

class AustriaKlarnaAddressCreator implements KlarnaAddressCreatorInterface
{
    /**
     * @param \Generated\Shared\Transfer\AddressTransfer $addressTransfer
     *
     * @return \KlarnaAddr
     */
    public function createKlarnaAddress(AddressTransfer $addressTransfer)
    {
        return new KlarnaAddr(
            $addressTransfer->getEmail(), // Email address
            $addressTransfer->getPhone(), // Telephone number, only one phone number is needed
            $addressTransfer->getCellPhone(), // Cell phone number
            utf8_decode($addressTransfer->getFirstName()), // First name (given name)
            utf8_decode($addressTransfer->getLastName()), // Last name (family name)
            '', // No care of, C/O
            utf8_decode($addressTransfer->getAddress1() . ' ' . $addressTransfer->getAddress2()), // Street address. For all countries except DE and NL, street and house number are sent together in the street field, separated by a space.
            $addressTransfer->getZipCode(), // Zip code
            utf8_decode($addressTransfer->getCity()), // City
            KlarnaCountry::fromCode($addressTransfer->getIso2Code()), // Country
            null, // House number (AT/DE/NL only)
            null                          // House extension (NL only)
        );
    }
}
