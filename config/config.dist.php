<?php

use Spryker\Shared\Application\ApplicationConstants;
use Spryker\Shared\Oms\OmsConstants;
use Spryker\Shared\Sales\SalesConstants;
use Spryker\Zed\Oms\OmsConfig;
use SprykerEco\Shared\Klarna\KlarnaConfig;
use SprykerEco\Shared\Klarna\KlarnaConstants;

$config[OmsConstants::PROCESS_LOCATION] = [
    OmsConfig::DEFAULT_PROCESS_LOCATION,
    APPLICATION_VENDOR_DIR . '/spryker-eco/klarna/config/Zed/Oms',
];

$config[OmsConstants::ACTIVE_PROCESSES] = [
    'KlarnaPayment01',
];

$config[SalesConstants::PAYMENT_METHOD_STATEMACHINE_MAPPING] = [
    KlarnaConfig::BRAND_INVOICE => 'KlarnaPayment01',
        KlarnaConfig::BRAND_INSTALLMENT => 'KlarnaPayment01',
];

/**
 * Klarna
 * Integration Requirements
 */
$config[KlarnaConstants::COUNTRY_AUSTRIA] = 'AT'; //EUR
$config[KlarnaConstants::COUNTRY_GERMANY] = 'DE'; //EUR
$config[KlarnaConstants::COUNTRY_NETHERLAND] = 'NL'; //EUR
$config[KlarnaConstants::COUNTRY_NORWAY] = 'NO'; //NOK
$config[KlarnaConstants::COUNTRY_SWEDEN] = 'SE'; //SEK
$config[KlarnaConstants::COUNTRY_FINLAND] = 'FI'; //EUR
$config[KlarnaConstants::COUNTRY_DENMARK] = 'DK'; //DKK

$config[KlarnaConstants::CURRENCY] = [
    $config[KlarnaConstants::COUNTRY_AUSTRIA] => 'EUR',
    $config[KlarnaConstants::COUNTRY_GERMANY] => 'EUR',
    $config[KlarnaConstants::COUNTRY_NETHERLAND] => 'EUR',
    $config[KlarnaConstants::COUNTRY_NORWAY] => 'NOK',
    $config[KlarnaConstants::COUNTRY_SWEDEN] => 'SEK',
    $config[KlarnaConstants::COUNTRY_FINLAND] => 'EUR',
    $config[KlarnaConstants::COUNTRY_DENMARK] => 'DKK',
];

/**
 * Klarna Testdata
 */
$config[KlarnaConstants::SHARED_SECRET] = 'pro2VDSakSISYFn';
$config[KlarnaConstants::EID] = '5373';
$config[KlarnaConstants::TEST_MODE] = true;
$config[KlarnaConstants::INVOICE_MAIL_TYPE] = KlarnaConfig::KLARNA_INVOICE_TYPE_EMAIL;
$config[KlarnaConstants::PCLASS_STORE_TYPE] = 'json';
$config[KlarnaConstants::PCLASS_STORE_URI] = APPLICATION_ROOT_DIR . '/data/DE/pclasses.json';

$domain = 'http://' . $config[ApplicationConstants::HOST_YVES];
$config[KlarnaConstants::CHECKOUT_CONFIRMATION_URI] = $domain . '/checkout/klarna/success';
$config[KlarnaConstants::CHECKOUT_TERMS_URI] = $domain;
$config[KlarnaConstants::CHECKOUT_PUSH_URI] = $domain . '/checkout/klarna/push';
$config[KlarnaConstants::CHECKOUT_URI] = $domain;
$config[KlarnaConstants::PDF_URL_PATTERN] = 'https://online.testdrive.klarna.com/invoices/%s.pdf';

// part payment options must not be displayed to the customer whenever the purchase sum exceeds €250.
$config[KlarnaConstants::NL_PART_PAYMENT_LIMIT] = 25000;
