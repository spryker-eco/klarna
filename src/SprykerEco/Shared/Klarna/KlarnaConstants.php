<?php

/**
 * MIT License
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace SprykerEco\Shared\Klarna;

/**
 * Interface KlarnaConstants
 *
 * @package SprykerEco\Shared\Klarna
 *
 * @author Daniel Bohnhardt <daniel.bohnhardt@twt.de>
 */
interface KlarnaConstants
{

    const PROVIDER_NAME = 'Klarna';
    const KLARNA_BUNDLE_VERSION = 'Spryker_KPM 1.0';

    const BRAND_INVOICE = 'KLARNA_INVOICE';
    const BRAND_INSTALLMENT = 'KLARNA_INSTALLMENT';
    const BRAND_CHECKOUT = 'KLARNA_CHECKOUT';

    const PAYMENT_METHOD_INVOICE = 'klarnaInvoice';
    const PAYMENT_METHOD_INSTALLMENT = 'klarnaInstallment';

    const PAYMENT_MEHTOD_INVOICE_NAME = 'INVOICE';
    const PAYMENT_METHOD_INSTALLMENT_NAME = 'INSTALLMENT';

    const SHARED_SECRED = 'KLARNA_SHARED_SECRET';
    const EID = 'KLARNA_EID';
    const TEST_MODE = 'KLARNA_TEST_MODE';

    const KLARNA_INVOICE_TYPE_MAIL = 2;
    const KLARNA_INVOICE_TYPE_EMAIL = 1;
    const KLARNA_INVOICE_TYPE_NOMAIL = 0;
    const KLARNA_ACTIVATE_SUCCESS = 'ok';
    const KLARNA_INVOICE_MAIL_TYPE = 'KLARNA_INVOICE_MAIL_TYPE';

    const KLARNA_PCLASS_STORE_URI = 'KLARNA_PCLASS_STORE_URI';
    const KLARNA_PCLASS_STORE_TYPE = 'KLARNA_PCLASS_STORE_TYPE';

    const KLARNA_CHECKOUT_TERMS_URI = 'KLARNA_CHECKOUT_TERMS_URI';
    const KLARNA_CHECKOUT_URI = 'KLARNA_CHECKOUT_URI';
    const KLARNA_CHECKOUT_CONFIRMATION_URI = 'KLARNA_CHECKOUT_CONFIRMATION_URI';
    const KLARNA_CHECKOUT_PUSH_URI = 'KLARNA_CHECKOUT_PUSH_URI';

    const KLARNA_PDF_URL_PATTERN = 'KLARNA_PDF_URL_PATTERN';

    const STATUS_COMPLETE = 'checkout_complete';
    const SHIPPING_TYPE = 'shipping_fee';

    const CHECKOUT_PAYMENT_METHOD = 'klarna_checkout';

    const CHECKOUT_API_MR = 'Herr';
    const CHECKOUT_API_MRS = 'Frau';

    const ORDER_PENDING_ACCEPTED = 1;
    const ORDER_PENDING_DENIED = 0;
    const ORDER_PENDING = 2;

    const COUNTRY_AUSTRIA = 'ISO_COUNTRY_AUSTRIA';
    const COUNTRY_GERMANY = 'ISO_COUNTRY_GERMANY';
    const COUNTRY_NETHERLAND = 'ISO_COUNTRY_NETHERLAND';
    const COUNTRY_NORWAY = 'ISO_COUNTRY_NORWAY';
    const COUNTRY_SWEDEN = 'ISO_COUNTRY_SWEDEN';
    const COUNTRY_FINLAND = 'ISO_COUNTRY_FINLAND';
    const COUNTRY_DENMARK = 'ISO_COUNTRY_DENMARK';

    const CURRENCY = 'CURRENCY';

    const NL_PART_PAYMENT_LIMIT = 'NL_PART_PAYMENT_LIMIT';

    const FIELD_PNO = 'pno_no';

}
