<?php

/**
 * MIT License
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace SprykerEco\Shared\Klarna;

interface KlarnaConstants
{
    const SHARED_SECRET = 'KLARNA:SHARED_SECRET';
    const EID = 'KLARNA:EID';
    const TEST_MODE = 'KLARNA:TEST_MODE';

    const INVOICE_MAIL_TYPE = 'KLARNA:INVOICE_MAIL_TYPE';

    const PCLASS_STORE_URI = 'KLARNA:PCLASS_STORE_URI';
    const PCLASS_STORE_TYPE = 'KLARNA:PCLASS_STORE_TYPE';

    const CHECKOUT_TERMS_URI = 'KLARNA:CHECKOUT_TERMS_URI';
    const CHECKOUT_URI = 'KLARNA:CHECKOUT_URI';
    const CHECKOUT_CONFIRMATION_URI = 'KLARNA:CHECKOUT_CONFIRMATION_URI';
    const CHECKOUT_PUSH_URI = 'KLARNA:CHECKOUT_PUSH_URI';

    const PDF_URL_PATTERN = 'KLARNA:PDF_URL_PATTERN';

    const COUNTRY_AUSTRIA = 'KLARNA:COUNTRY_AUSTRIA';
    const COUNTRY_GERMANY = 'KLARNA:COUNTRY_GERMANY';
    const COUNTRY_NETHERLAND = 'KLARNA:COUNTRY_NETHERLAND';
    const COUNTRY_NORWAY = 'KLARNA:COUNTRY_NORWAY';
    const COUNTRY_SWEDEN = 'KLARNA:COUNTRY_SWEDEN';
    const COUNTRY_FINLAND = 'KLARNA:COUNTRY_FINLAND';
    const COUNTRY_DENMARK = 'KLARNA:COUNTRY_DENMARK';

    const CURRENCY = 'KLARNA:CURRENCY';

    const NL_PART_PAYMENT_LIMIT = 'KLARNA:NL_PART_PAYMENT_LIMIT';
}
