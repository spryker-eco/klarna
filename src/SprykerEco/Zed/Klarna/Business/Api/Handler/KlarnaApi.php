<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace SprykerEco\Zed\Klarna\Business\Api\Handler;

use DateTime;
use Exception;
use Generated\Shared\Transfer\AddressTransfer;
use Generated\Shared\Transfer\KlarnaCheckoutServiceRequestTransfer;
use Generated\Shared\Transfer\KlarnaGetAddressesRequestTransfer;
use Generated\Shared\Transfer\KlarnaObjectInitTransfer;
use Generated\Shared\Transfer\KlarnaPaymentTransfer;
use Generated\Shared\Transfer\KlarnaPClassRequestTransfer;
use Generated\Shared\Transfer\OrderTransfer;
use Generated\Shared\Transfer\QuoteTransfer;
use Generated\Shared\Transfer\ShipmentTransfer;
use Klarna;
use KlarnaCountry;
use KlarnaCurrency;
use KlarnaEncoding;
use KlarnaException;
use KlarnaFlags;
use KlarnaPClass;
use Klarna_UnknownEncodingException;
use Orm\Zed\Klarna\Persistence\SpyPaymentKlarna;
use Orm\Zed\Klarna\Persistence\SpyPaymentKlarnaTransactionStatusLog;
use Spryker\Shared\Library\Currency\CurrencyManager;
use SprykerEco\Shared\Klarna\KlarnaConstants;
use SprykerEco\Zed\Klarna\Dependency\Facade\KlarnaToLocaleInterface;

/**
 * Class KlarnaApi
 *
 * @package SprykerEco\Zed\Klarna\Business\Api
 *
 * @author Daniel Bohnhardt <daniel.bohnhardt@twt.de>
 */
class KlarnaApi
{

    const ISO_CODE_DE = 'DE';

    const ISO_CODE_NL = 'NL';

    const ISO_CODE_AT = 'AT';

    const DEFAULT_TAX_RATE = 19;

    const KLARNA_SHIPPING_ARTICLE_TYPE = 'shipping';

    const UPDATE_SUCCESS = 'ok';
    const UPDATE_ERROR = 'error';

    const NON_PNO_COUNTRIES = [self::ISO_CODE_DE, self::ISO_CODE_NL, self::ISO_CODE_AT];

    /**
     * @var string
     *
     * @author Daniel Bohnhardt <daniel.bohnhardt@twt.de>
     */
    protected $merchantId;

    /**
     * @var string
     *
     * @author Daniel Bohnhardt <daniel.bohnhardt@twt.de>
     */
    protected $sharedSecret;

    /**
     * @var bool
     *
     * @author Daniel Bohnhardt <daniel.bohnhardt@twt.de>
     */
    protected $testMode;

    /**
     * @var \Klarna
     *
     * @author Daniel Bohnhardt <daniel.bohnhardt@twt.de>
     */
    protected $klarnaAdapter;

    /**
     * @var int
     *
     * @author Daniel Bohnhardt <daniel.bohnhardt@twt.de>
     */
    protected $mailMode;

    /**
     * @var string
     *
     * @author Daniel Bohnhardt <daniel.bohnhardt@twt.de>
     */
    protected $pClassStoreType;

    /**
     * @var string
     *
     * @author Daniel Bohnhardt <daniel.bohnhardt@twt.de>
     */
    protected $pClassStoreUri;

    /**
     * @var \SprykerEco\Zed\Klarna\Dependency\Facade\KlarnaToLocaleInterface
     */
    protected $localeFacade;

    /**
     * @var \SprykerEco\Zed\Klarna\Business\Api\Handler\KlarnaAddressFactory
     */
    protected $klarnaCountryFactory;

    /**
     * KlarnaApi constructor.
     *
     * @author Daniel Bohnhardt <daniel.bohnhardt@twt.de>
     *
     * @param \Klarna $klarnaAdapter
     * @param string $merchantId
     * @param string $sharedSecret
     * @param bool $testMode
     * @param int $mailMode
     * @param string $pClassStoreType
     * @param string $pClassStoreUri
     * @param \SprykerEco\Zed\Klarna\Dependency\Facade\KlarnaToLocaleInterface $localeFacade
     */
    public function __construct(
        $klarnaAdapter,
        $merchantId,
        $sharedSecret,
        $testMode,
        $mailMode,
        $pClassStoreType,
        $pClassStoreUri,
        KlarnaToLocaleInterface $localeFacade
    ) {
        $this->klarnaAdapter = $klarnaAdapter;
        $this->merchantId = $merchantId;
        $this->sharedSecret = $sharedSecret;
        $this->testMode = $testMode;
        $this->mailMode = $mailMode;
        $this->pClassStoreType = $pClassStoreType;
        $this->pClassStoreUri = $pClassStoreUri;
        $this->localeFacade = $localeFacade;

        $this->klarnaCountryFactory = new KlarnaAddressFactory();
    }

    /**
     * Creates the basic klarna object.
     *
     * @author Daniel Bohnhardt <daniel.bohnhardt@twt.de>
     *
     * @param string $country
     * @param string $currency
     *
     * @return \Klarna
     */
    protected function createKlarnaObject($country, $currency)
    {
        $countryCode = KlarnaCountry::fromCode($country);
        $klarnaApi = clone $this->klarnaAdapter;

        $klarnaApi->config(
            $this->merchantId, // Merchant ID
            $this->sharedSecret, // Shared secret
            $countryCode, // Purchase country
            KlarnaCountry::getLanguage($countryCode), // Purchase language
            KlarnaCurrency::fromCode($currency), // Purchase currency
            (($this->testMode) ? Klarna::BETA : Klarna::LIVE), // Server
            $this->pClassStoreType, // PClass storage
            $this->pClassStoreUri // PClass storage URI path
        );

        return $klarnaApi;
    }

    /**
     * @param \Generated\Shared\Transfer\KlarnaObjectInitTransfer $klarnaObjectInitTransfer
     *
     * @return \Klarna
     */
    protected function createKlarnaObjectByKlarnaObjectInitTransfer(KlarnaObjectInitTransfer $klarnaObjectInitTransfer)
    {
        $klarnaApi = $this->createKlarnaObject(
            $klarnaObjectInitTransfer->getIso2Code(),
            $klarnaObjectInitTransfer->getCurrencyIso3Code()
        );
        $klarnaApi->setClientIP($klarnaObjectInitTransfer->getClientIp());

        return $klarnaApi;
    }

    /**
     * @param \Generated\Shared\Transfer\AddressTransfer $addressTransfer
     *
     * @return \Klarna
     */
    protected function createKlarnaObjectByAddressTransfer(AddressTransfer $addressTransfer)
    {
        return $this->createKlarnaObject(
            $addressTransfer->getIso2Code(),
            $addressTransfer->getCurrencyIso3Code()
        );
    }

    /**
     * @param \Orm\Zed\Klarna\Persistence\SpyPaymentKlarna $paymentEntity
     *
     * @return \Klarna
     */
    protected function createKlarnaObjectByPaymentKlarnaEntity(SpyPaymentKlarna $paymentEntity)
    {
        $klarnaApi = $this->createKlarnaObject(
            $paymentEntity->getLanguageIso2Code(),
            $paymentEntity->getCurrencyIso3Code()
        );
        $klarnaApi->setClientIP($paymentEntity->getClientIp());

        return $klarnaApi;
    }

    /**
     * @param \Generated\Shared\Transfer\KlarnaPaymentTransfer $paymentTransfer
     *
     * @return \Klarna
     */
    protected function createKlarnaObjectByKlarnaPaymentTransfer(KlarnaPaymentTransfer $paymentTransfer)
    {
        $paymentEntity = new SpyPaymentKlarna();
        $paymentEntity->fromArray($paymentTransfer->toArray());

        return $this->createKlarnaObjectByPaymentKlarnaEntity($paymentEntity);
    }

    /**
     * Activate Invoice at klarna.
     *
     * @author Daniel Bohnhardt <daniel.bohnhardt@twt.de>
     *
     * @param \Orm\Zed\Klarna\Persistence\SpyPaymentKlarna $paymentEntity
     * @param \Generated\Shared\Transfer\OrderTransfer $orderTransfer
     *
     * @return array
     */
    public function activateOrder(SpyPaymentKlarna $paymentEntity, OrderTransfer $orderTransfer)
    {
        $klarnaApi = $this->createKlarnaObjectByPaymentKlarnaEntity($paymentEntity);
        $klarnaApi->setEstoreInfo($orderTransfer->getOrderReference(), $orderTransfer->getInvoiceReference());

        $klarnaApi->setActivateInfo('orderid1', $orderTransfer->getOrderReference());
        $klarnaApi->setActivateInfo('orderid2', $orderTransfer->getInvoiceReference());

        try {
            $mailMode = ($this->mailMode === KlarnaConstants::KLARNA_INVOICE_TYPE_NOMAIL)
                ? null
                : ($this->mailMode === KlarnaConstants::KLARNA_INVOICE_TYPE_MAIL)
                    ? KlarnaFlags::RSRV_SEND_BY_MAIL
                    : KlarnaFlags::RSRV_SEND_BY_EMAIL;
            $result = $klarnaApi->activate(
                $paymentEntity->getPreCheckId(),
                null, // OCR Number
                $mailMode
            );
            $msg = isset($result[1])?$result[1]:'';
            $this->logApiResult('activate', $paymentEntity->getIdPaymentKlarna(), $result[0], $msg);
        } catch (KlarnaException $exception) {
            $result = [0, '', $exception->getMessage()];
            $this->logApiResult(
                'activateError',
                $paymentEntity->getIdPaymentKlarna(),
                0,
                $exception->getMessage(),
                $exception->getCode()
            );
        }

        return $result;
    }

    /**
     * Activate Invoice at klarna.
     *
     * @author Daniel Bohnhardt <daniel.bohnhardt@twt.de>
     *
     * @param array $spyOrderItems
     * @param \Orm\Zed\Klarna\Persistence\SpyPaymentKlarna $paymentEntity
     * @param \Generated\Shared\Transfer\OrderTransfer $orderTransfer
     *
     * @return array
     */
    public function activatePartOrder(array $spyOrderItems, SpyPaymentKlarna $paymentEntity, OrderTransfer $orderTransfer)
    {
        $klarnaApi = $this->createKlarnaObjectByPaymentKlarnaEntity($paymentEntity);
        $klarnaApi->setEstoreInfo($orderTransfer->getOrderReference(), $orderTransfer->getInvoiceReference());

        $klarnaApi->setActivateInfo('orderid1', $orderTransfer->getOrderReference());
        $klarnaApi->setActivateInfo('orderid2', $orderTransfer->getInvoiceReference());

        $orderItems = [];
        /** @var \Orm\Zed\Sales\Persistence\SpySalesOrderItem $spyOrderItem */
        foreach ($spyOrderItems as $spyOrderItem) {
            if (!isset($orderItems[$spyOrderItem->getSku()])) {
                $orderItems[$spyOrderItem->getSku()] = $spyOrderItem->getQuantity();
            } else {
                $orderItems[$spyOrderItem->getSku()] += $spyOrderItem->getQuantity();
            }
        }
        foreach ($orderItems as $itemSku => $quantity) {
            $klarnaApi->addArtNo($quantity, $itemSku);
        }

        // activate shipment with first item
        $activateShipping = false;
        if (!$paymentEntity->getShippingInvoiceId()) {
            $klarnaApi->addArtNo(1, self::KLARNA_SHIPPING_ARTICLE_TYPE);
            $activateShipping = true;
        }

        try {
            $result = $klarnaApi->activate(
                $paymentEntity->getPreCheckId(),
                null, // OCR Number
                ($this->mailMode === KlarnaConstants::KLARNA_INVOICE_TYPE_MAIL)
                ? KlarnaFlags::RSRV_SEND_BY_MAIL
                : KlarnaFlags::RSRV_SEND_BY_EMAIL
            );
            $msg = isset($result[1])?$result[1]:'';
            $this->logApiResult('activatePart', $paymentEntity->getIdPaymentKlarna(), $result[0], $msg);
            if ($activateShipping) {
                $this->logApiResult('activateShipping', $paymentEntity->getIdPaymentKlarna(), $result[0], $msg);
            }

        } catch (KlarnaException $exception) {
            $result = [0, '', $exception->getMessage()];
            $this->logApiResult(
                'activatePartError',
                $paymentEntity->getIdPaymentKlarna(),
                0,
                $exception->getMessage(),
                $exception->getCode()
            );
        }

        return $result;
    }

    /**
     * Get Part Payments.
     *
     * @author Daniel Bohnhardt <daniel.bohnhardt@twt.de>
     *
     * @param \Generated\Shared\Transfer\KlarnaPClassRequestTransfer $klarnaPClassRequestTransfer
     *
     * @return \KlarnaPClass[]
     */
    public function getPclasses(KlarnaPClassRequestTransfer $klarnaPClassRequestTransfer)
    {
        $klarnaApi = $this->createKlarnaObjectByKlarnaObjectInitTransfer($klarnaPClassRequestTransfer->getKlarnaObjectInit());

        try {
            $pClasses = $klarnaApi->getPClasses($klarnaPClassRequestTransfer->getPClassType());
            if (!$pClasses) {
                $klarnaApi->fetchPClasses();
                $pClasses = $klarnaApi->getPClasses($klarnaPClassRequestTransfer->getPClassType());
                $klarnaApi->sortPClasses($pClasses);
            }
        } catch (KlarnaException $exception) {
            $pClasses = [];
        }

        return $pClasses;
    }

    /**
     * Refund Invoice.
     *
     * @author Daniel Bohnhardt <daniel.bohnhardt@twt.de>
     *
     * @param \Orm\Zed\Klarna\Persistence\SpyPaymentKlarna $paymentEntity
     *
     * @throws \KlarnaException
     *
     * @return string
     */
    public function creditInvoice(SpyPaymentKlarna $paymentEntity)
    {
        $klarnaApi = $this->createKlarnaObjectByPaymentKlarnaEntity($paymentEntity);

        try {
            $result = $klarnaApi->creditInvoice($paymentEntity->getInvoiceId());
            $this->logApiResult('credit', $paymentEntity->getIdPaymentKlarna(), $result);
        } catch (KlarnaException $exception) {
            $this->logApiResult(
                'creditError',
                $paymentEntity->getIdPaymentKlarna(),
                0,
                $exception->getMessage(),
                $exception->getCode()
            );

            throw $exception;
        }

        return $result;
    }

    /**
     * activate Shipment in separate call
     *
     * @param \Orm\Zed\Klarna\Persistence\SpyPaymentKlarna $paymentEntity
     *
     * @throws \KlarnaException
     *
     * @return void
     */
    public function ship(SpyPaymentKlarna $paymentEntity)
    {
        if ($paymentEntity->getShippingInvoiceId() ||
            !$paymentEntity->getSpySalesOrder()->getShipmentMethod()->getDefaultPrice()
        ) {
            return;
        }

        $klarnaApi = $this->createKlarnaObjectByPaymentKlarnaEntity($paymentEntity);
        try {
            $klarnaApi->addArtNo(1, self::KLARNA_SHIPPING_ARTICLE_TYPE);
            $shippingActivationResult = $klarnaApi->activate(
                $paymentEntity->getPreCheckId()
            );
            $msg = isset($shippingActivationResult[1])?$shippingActivationResult[1]:'';
            $this->logApiResult('activateShipping', $paymentEntity->getIdPaymentKlarna(), $shippingActivationResult[0], $msg);

            if ($shippingActivationResult[0] === KlarnaConstants::KLARNA_ACTIVATE_SUCCESS) {
                $paymentEntity->setShippingInvoiceId($shippingActivationResult[1]);
                $paymentEntity->save();
            }

        } catch (KlarnaException $exception) {
            $this->logApiResult(
                'activateShippingError',
                $paymentEntity->getIdPaymentKlarna(),
                0,
                $exception->getMessage(),
                $exception->getCode()
            );

            throw $exception;
        }
    }

    /**
     * Refund article.
     *
     * @author Daniel Bohnhardt <daniel.bohnhardt@twt.de>
     *
     * @param array $orderItems
     * @param \Orm\Zed\Klarna\Persistence\SpyPaymentKlarna $paymentEntity
     *
     * @throws \KlarnaException
     *
     * @return string
     */
    public function creditPart(array $orderItems, SpyPaymentKlarna $paymentEntity)
    {
        $klarnaApi = $this->createKlarnaObjectByPaymentKlarnaEntity($paymentEntity);

        try {
            $result = '';
            $klarnaOrderItems = $paymentEntity->getSpyPaymentKlarnaOrderItems();
            $items = [];
            /** @var \Orm\Zed\Sales\Persistence\SpySalesOrderItem $orderItem */
            foreach ($orderItems as $orderItem) {
                $items[$orderItem->getIdSalesOrderItem()] = [
                    'fkOrderItem' => $orderItem->getIdSalesOrderItem(),
                    'quantity' => $orderItem->getQuantity(),
                    'sku' => $orderItem->getSku(),
                ];
            }

            $invoiceItems = [];
            foreach ($klarnaOrderItems as $klarnaOrderItem) {
                if (isset($items[$klarnaOrderItem->getFkSalesOrderItem()])) {
                    $item = $items[$klarnaOrderItem->getFkSalesOrderItem()];
                    $quantity = $item['quantity'];
                    $sku = $item['sku'];
                    $invoiceId = $klarnaOrderItem->getInvoiceId();
                    if (!isset($invoiceItems[$invoiceId])) {
                        $invoiceItems[$invoiceId] = [];
                    }
                    if (!isset($invoiceItems[$invoiceId][$sku])) {
                        $invoiceItems[$invoiceId][$sku] = $quantity;
                    } else {
                        $invoiceItems[$invoiceId][$sku] += $quantity;
                    }
                }
            }

            foreach ($invoiceItems as $invoiceId => $invoiceItemSku) {
                foreach ($invoiceItemSku as $sku => $quantity) {
                    $klarnaApi->addArtNo($quantity, $sku);
                }

                $creditShipping = false;
                if ($paymentEntity->getShippingInvoiceId() == $invoiceId) {
                    $klarnaApi->addArtNo(1, self::KLARNA_SHIPPING_ARTICLE_TYPE);
                    $creditShipping = true;
                }

                $result = $klarnaApi->creditPart($invoiceId);
                $this->logApiResult('creditPart', $paymentEntity->getIdPaymentKlarna(), 'ok', $result);
                if ($creditShipping) {
                    $this->logApiResult('creditShipping', $paymentEntity->getIdPaymentKlarna(), 'ok', $result);
                }
            }

        } catch (KlarnaException $exception) {
            $this->logApiResult(
                'creditPartError',
                $paymentEntity->getIdPaymentKlarna(),
                0,
                $exception->getMessage(),
                $exception->getCode()
            );

            throw $exception;
        }

        return $result;
    }

    /**
     * Refund shipping in separate call
     *
     * @param \Klarna $klarnaApi
     * @param \Orm\Zed\Klarna\Persistence\SpyPaymentKlarna $paymentEntity
     *
     * @return void
     */
    protected function creditShipping(Klarna $klarnaApi, SpyPaymentKlarna $paymentEntity)
    {
        try {
            if ($paymentEntity->getShippingInvoiceId()) {
                $klarnaApi->addArtNo(1, self::KLARNA_SHIPPING_ARTICLE_TYPE);
                $shippingRefundResult = $klarnaApi->creditPart(
                    $paymentEntity->getShippingInvoiceId()
                );
                $this->logApiResult('creditShipping', $paymentEntity->getIdPaymentKlarna(), 'ok', $shippingRefundResult);
            }

        } catch (Exception $exception) {
            $this->logApiResult(
                'creditShippingError',
                $paymentEntity->getIdPaymentKlarna(),
                0,
                $exception->getMessage(),
                $exception->getCode()
            );
        }
    }

    /**
     * Cancel reservation.
     *
     * @author Daniel Bohnhardt <daniel.bohnhardt@twt.de>
     *
     * @param \Orm\Zed\Klarna\Persistence\SpyPaymentKlarna $paymentEntity
     *
     * @return bool
     */
    public function cancelReservation(SpyPaymentKlarna $paymentEntity)
    {
        $klarnaApi = $this->createKlarnaObjectByPaymentKlarnaEntity($paymentEntity);

        try {
            $result = $klarnaApi->cancelReservation($paymentEntity->getPreCheckId());

            $this->logApiResult('cancel', $paymentEntity->getIdPaymentKlarna(), $result);

        } catch (KlarnaException $exception) {
            $this->logApiResult(
                'cancelError',
                $paymentEntity->getIdPaymentKlarna(),
                0,
                $exception->getMessage(),
                $exception->getCode()
            );
            $result = false;
        }

        return $result;
    }

    /**
     * Reserve Invoice Amount.
     *
     * @author Daniel Bohnhardt <daniel.bohnhardt@twt.de>
     *
     * @param \Generated\Shared\Transfer\QuoteTransfer $quoteTransfer
     *
     * @return array
     */
    public function reserveAmount(QuoteTransfer $quoteTransfer)
    {
        $paymentTransfer = $quoteTransfer->getPayment()->getKlarna();
        $countryIsoCode = $paymentTransfer->getLanguageIso2Code();
        $klarnaApi = $this->createKlarnaObjectByKlarnaPaymentTransfer($paymentTransfer);

        $this->addReservationValues($quoteTransfer, $paymentTransfer, $klarnaApi);

        if (in_array($countryIsoCode, self::NON_PNO_COUNTRIES)) {
            $date = new DateTime($paymentTransfer->getDateOfBirth());
            $pno = $date->format('dmY');
        } else {
            $encoding = KlarnaEncoding::get($countryIsoCode);
            try {
                $pnoRegEx = KlarnaEncoding::getRegexp($encoding);
            } catch (Klarna_UnknownEncodingException $klarnaLibException) {
                return [
                    0, '', $klarnaLibException->getMessage(),
                ];
            }
            $pno = $paymentTransfer->getPnoNo();
            if (!preg_match($pnoRegEx, $pno)) {
                return [
                    0, '', 'wrong pno number',
                ];
            }
        }

        try {
            $pclass = ($paymentTransfer->getAccountBrand() === KlarnaConstants::BRAND_INSTALLMENT)
                ? $paymentTransfer->getInstallmentPayIndex()
                : KlarnaPClass::INVOICE;

            $result = $klarnaApi->reserveAmount(
                $pno, // PNO (Date of birth for AT/DE/NL)
                (($paymentTransfer->getGender() == 0) ? KlarnaFlags::MALE : KlarnaFlags::FEMALE), // KlarnaFlags::MALE, KlarnaFlags::FEMALE (AT/DE/NL only)
                - 1, // Automatically calculate and reserve the cart total amount
                KlarnaFlags::NO_FLAG,
                $pclass
            );

        } catch (KlarnaException $exception) {

            $result = [
                0, '', utf8_encode($exception->getMessage()),
            ];
        }

        return $result;
    }

    /**
     * Add reservation values to passed klarna instance.
     *
     * @author Daniel Bohnhardt <daniel.bohnhardt@twt.de>
     *
     * @param \Generated\Shared\Transfer\QuoteTransfer $quoteTransfer
     * @param \Generated\Shared\Transfer\KlarnaPaymentTransfer $paymentTransfer
     * @param \Klarna $klarnaApi
     *
     * @return void
     */
    protected function addReservationValues(
        QuoteTransfer $quoteTransfer,
        KlarnaPaymentTransfer $paymentTransfer,
        $klarnaApi
    ) {
        $billingAddress = $quoteTransfer->getBillingAddress();
        $klarnaBillingAddress = $this->createKlarnaAddress($billingAddress, $paymentTransfer);

        $shippingAddress = $quoteTransfer->getShippingAddress();
        if ($shippingAddress !== null) {
            $klarnaShippingAddress = $this->createKlarnaAddress($shippingAddress, $paymentTransfer);
        } else {
            $klarnaShippingAddress = $klarnaBillingAddress;
        }

        $klarnaApi->setAddress(KlarnaFlags::IS_BILLING, $klarnaBillingAddress);
        $klarnaApi->setAddress(KlarnaFlags::IS_SHIPPING, $klarnaShippingAddress);

        $this->addOrderItems($quoteTransfer->getItems(), $klarnaApi);

        $this->addShipping($quoteTransfer->getShipment(), $klarnaApi);
    }

    /**
     * @param \Generated\Shared\Transfer\AddressTransfer $addressTransfer
     * @param \Generated\Shared\Transfer\OrderTransfer $salesOrderTransfer
     * @param \Orm\Zed\Klarna\Persistence\SpyPaymentKlarna $spyPayment
     *
     * @return void
     */
    public function updateAddress(
        AddressTransfer $addressTransfer,
        OrderTransfer $salesOrderTransfer,
        SpyPaymentKlarna $spyPayment
    ) {
        $klarnaApi = $this->createKlarnaObjectByAddressTransfer($addressTransfer);
        $klarnaAddress = $this->createKlarnaAddress($addressTransfer);

        $addressType = null;
        if ($salesOrderTransfer->getShippingAddress()->getIdSalesOrderAddress() == $addressTransfer->getIdSalesOrderAddress()) {
            $addressType = KlarnaFlags::IS_SHIPPING;
        } elseif ($salesOrderTransfer->getBillingAddress()->getIdSalesOrderAddress() == $addressTransfer->getIdSalesOrderAddress()) {
            $addressType = KlarnaFlags::IS_BILLING;
        }

        if ($addressType !== null) {
            $klarnaApi->setAddress($addressType, $klarnaAddress);
            $klarnaApi->update($spyPayment->getPreCheckId());
        }
    }

    /**
     * Add order items to klarna instance.
     *
     * @author Daniel Bohnhardt <daniel.bohnhardt@twt.de>
     *
     * @param array $items
     * @param \Klarna $klarnaApi
     *
     * @return void
     */
    public function addOrderItems($items, $klarnaApi)
    {
        $flags = KlarnaFlags::INC_VAT;
        /** @var \Generated\Shared\Transfer\ItemTransfer $orderItem */
        foreach ($items as $orderItem) {
            $discountPercentage = 0;

            $discount = $orderItem->getUnitTotalDiscountAmount();
            if ($discount) {
                $total = $orderItem->getUnitGrossPrice();
                $discountPercentage = round((100 / $total) * $discount, 2);
            }

            $klarnaApi->addArticle(
                $orderItem->getQuantity(),
                $orderItem->getSku(),
                $orderItem->getName(),
                $this->getCurrencyManager()->convertCentToDecimal($orderItem->getUnitGrossPrice()),
                ($orderItem->getTaxRate()) ?: self::DEFAULT_TAX_RATE,
                $discountPercentage,
                $flags
            );
        }
    }

    /**
     * Convert addressTransfer to klarna address.
     *
     * @author Daniel Bohnhardt <daniel.bohnhardt@twt.de>
     *
     * @param \Generated\Shared\Transfer\AddressTransfer $addressTransfer
     * @param \Generated\Shared\Transfer\KlarnaPaymentTransfer|null $paymentTransfer
     *
     * @return \KlarnaAddr
     */
    public function createKlarnaAddress(AddressTransfer $addressTransfer, KlarnaPaymentTransfer $paymentTransfer = null)
    {
        if (!$addressTransfer->getEmail() && $paymentTransfer !== null) {
            $addressTransfer->setEmail($paymentTransfer->getEmail());
        }
        if (!$addressTransfer->getPhone()
            && !$addressTransfer->getCellPhone()
            && $paymentTransfer !== null
        ) {
            $addressTransfer->setPhone($paymentTransfer->getPhone());
        }

        $klarnaAddressCreator = $this->klarnaCountryFactory
            ->createKlarnaAddressCreator($addressTransfer->getIso2Code());

        $klarnaAddress = $klarnaAddressCreator->createKlarnaAddress($addressTransfer);

        return $klarnaAddress;
    }

    /**
     * Check order Status
     *
     * @author Daniel Bohnhardt <daniel.bohnhardt@twt.de>
     *
     * @param \Orm\Zed\Klarna\Persistence\SpyPaymentKlarna $paymentEntity
     *
     * @return int
     */
    public function checkOrderStatus(SpyPaymentKlarna $paymentEntity)
    {
        if ((int)$paymentEntity->getStatus() == KlarnaConstants::ORDER_PENDING_ACCEPTED) {
            return KlarnaConstants::ORDER_PENDING_ACCEPTED;
        }

        $klarnaApi = $this->createKlarnaObjectByPaymentKlarnaEntity($paymentEntity);
        $status = (int)$klarnaApi->checkOrderStatus($paymentEntity->getPreCheckId());

        if ($status === KlarnaFlags::ACCEPTED) {
            $return = KlarnaConstants::ORDER_PENDING_ACCEPTED;
        } elseif ($status === KlarnaFlags::DENIED) {
            $return = KlarnaConstants::ORDER_PENDING_DENIED;
        } else {
            $return = KlarnaConstants::ORDER_PENDING;
        }

        $this->logApiResult('checkOrderStatus', $paymentEntity->getIdPaymentKlarna(), $status, "Klarna status:" . $status . "; our:" . $return);

        return $return;
    }

    /**
     * @return \Spryker\Shared\Library\Currency\CurrencyManager
     */
    protected function getCurrencyManager()
    {
        return CurrencyManager::getInstance();
    }

    /**
     * Add Shipping costs to klarna.
     *
     * @author Daniel Bohnhardt <daniel.bohnhardt@twt.de>
     *
     * @param \Generated\Shared\Transfer\ShipmentTransfer $shipment
     * @param \Klarna $klarnaApi
     *
     * @return void
     */
    protected function addShipping(ShipmentTransfer $shipment, $klarnaApi)
    {
        $method = $shipment->getMethod();
        if ($method->getDefaultPrice()) {
            $klarnaApi->addArticle(
                1,
                self::KLARNA_SHIPPING_ARTICLE_TYPE,
                $method->getName(),
                $this->getCurrencyManager()->convertCentToDecimal($method->getDefaultPrice()),
                (float)$method->getTaxRate(),
                0,
                KlarnaFlags::INC_VAT | KlarnaFlags::IS_SHIPMENT
            );
        }
    }

    /**
     * Update an reservation.
     *
     * @author Daniel Bohnhardt <daniel.bohnhardt@twt.de>
     *
     * @param \Generated\Shared\Transfer\QuoteTransfer $quoteTransfer
     *
     * @return string
     */
    public function update(QuoteTransfer $quoteTransfer)
    {
        $paymentTransfer = $quoteTransfer->getPayment()->getKlarna();
        $klarnaApi = $this->createKlarnaObjectByKlarnaPaymentTransfer($paymentTransfer);

        $this->addReservationValues($quoteTransfer, $paymentTransfer, $klarnaApi);
        try {
            $isUpdated = $klarnaApi->update($paymentTransfer->getPreCheckId());
            $result = $isUpdated ? self::UPDATE_SUCCESS : self::UPDATE_ERROR;

            $this->logApiResult('update', $paymentTransfer->getFkSalesOrder(), $result);

        } catch (KlarnaException $exception) {
            $this->logApiResult(
                'updateError',
                $paymentTransfer->getFkSalesOrder(),
                0,
                $exception->getMessage(),
                $exception->getCode()
            );

            $result = $exception->getMessage();
        }

        return $result;
    }

    /**
     * Send invoice by email.
     *
     * @author Daniel Bohnhardt <daniel.bohnhardt@twt.de>
     *
     * @param \Orm\Zed\Klarna\Persistence\SpyPaymentKlarna $paymentEntity
     *
     * @return string
     */
    public function sendInvoiceByMail(SpyPaymentKlarna $paymentEntity)
    {
        $klarnaApi = $this->createKlarnaObjectByPaymentKlarnaEntity($paymentEntity);

        return $klarnaApi->emailInvoice($paymentEntity->getInvoiceId());
    }

    /**
     * Send invoice by postal service.
     *
     * @author Daniel Bohnhardt <daniel.bohnhardt@twt.de>
     *
     * @param \Orm\Zed\Klarna\Persistence\SpyPaymentKlarna $paymentEntity
     *
     * @return string
     */
    public function sendInvoiceByPost(SpyPaymentKlarna $paymentEntity)
    {
        $klarnaApi = $this->createKlarnaObjectByPaymentKlarnaEntity($paymentEntity);

        return $klarnaApi->sendInvoice($paymentEntity->getInvoiceId());
    }

    /**
     * Perform a checkout service request.
     *
     * @param \Generated\Shared\Transfer\KlarnaCheckoutServiceRequestTransfer $klarnaCheckoutServiceRequestTransfer
     *
     * @return \CheckoutServiceResponse
     */
    public function checkoutService(KlarnaCheckoutServiceRequestTransfer $klarnaCheckoutServiceRequestTransfer)
    {
        $klarnaApi = $this->createKlarnaObjectByKlarnaObjectInitTransfer($klarnaCheckoutServiceRequestTransfer->getKlarnaObjectInit());

        $country = $klarnaCheckoutServiceRequestTransfer->getKlarnaObjectInit()->getIso2Code();
        $currency = $klarnaCheckoutServiceRequestTransfer->getKlarnaObjectInit()->getCurrencyIso3Code();
        $price = $klarnaCheckoutServiceRequestTransfer->getGrandTotal() / 100;
        $locale = $this->localeFacade->getCurrentLocaleName();

        return $klarnaApi->checkoutService(
            $price,
            $currency,
            $locale,
            $country
        );
    }

    /**
     * Get Addresses. Method get_addresses
     *
     * @param \Generated\Shared\Transfer\KlarnaGetAddressesRequestTransfer $klarnaGetAddressesRequestTransfer
     *
     * @return array
     */
    public function getAddresses(KlarnaGetAddressesRequestTransfer $klarnaGetAddressesRequestTransfer)
    {
        $klarnaApi = $this->createKlarnaObjectByKlarnaObjectInitTransfer($klarnaGetAddressesRequestTransfer->getKlarnaObjectInit());

        return $klarnaApi->getAddresses($klarnaGetAddressesRequestTransfer->getPno());
    }

    /**
     * Log Api Results to transaction status table.
     *
     * @author Daniel Bohnhardt <daniel.bohnhardt@twt.de>
     *
     * @param string $type
     * @param string $paymentId
     * @param string $status
     * @param string $errorMsg
     * @param int $errorCode
     *
     * @return void
     */
    protected function logApiResult($type, $paymentId, $status, $errorMsg = '', $errorCode = 0)
    {
        $logEntity = new SpyPaymentKlarnaTransactionStatusLog();
        $logEntity->setProcessingType($type);
        $logEntity->setProcessingStatus($status);
        $logEntity->setProcessingErrorMessage($errorMsg);
        $logEntity->setProcessingErrorCode($errorCode);
        $logEntity->setFkPaymentKlarna($paymentId);

        $logEntity->save();
    }

}
