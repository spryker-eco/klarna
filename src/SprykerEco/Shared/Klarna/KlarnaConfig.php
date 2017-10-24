<?php

/**
 * MIT License
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace SprykerEco\Shared\Klarna;

interface KlarnaConfig
{
    const PROVIDER_NAME = 'Klarna';
    const KLARNA_BUNDLE_VERSION = 'Spryker_KPM 1.0';

    const BRAND_INVOICE = 'KLARNA_INVOICE';
    const BRAND_INSTALLMENT = 'KLARNA_INSTALLMENT';
    const BRAND_CHECKOUT = 'KLARNA_CHECKOUT';

    const PAYMENT_METHOD_INVOICE = 'klarnaInvoice';
    const PAYMENT_METHOD_INSTALLMENT = 'klarnaInstallment';

    const KLARNA_INVOICE_TYPE_MAIL = 2;
    const KLARNA_INVOICE_TYPE_EMAIL = 1;
    const KLARNA_INVOICE_TYPE_NOMAIL = 0;
    const KLARNA_ACTIVATE_SUCCESS = 'ok';
    const STATUS_COMPLETE = 'checkout_complete';
    const SHIPPING_TYPE = 'shipping_fee';

    const CHECKOUT_PAYMENT_METHOD = 'klarna_checkout';

    const CHECKOUT_API_MR = 'Herr';
    const CHECKOUT_API_MRS = 'Frau';

    const ORDER_PENDING_ACCEPTED = 1;
    const ORDER_PENDING_DENIED = 0;
    const ORDER_PENDING = 2;

    const FIELD_PNO = 'pno_no';
}
