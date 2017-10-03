<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace SprykerEco\Zed\Klarna\Business\Api\Handler;

use Exception;
use Generated\Shared\Transfer\KlarnaCheckoutTransfer;
use Generated\Shared\Transfer\QuoteTransfer;
use Klarna_Checkout_Order;
use Orm\Zed\Customer\Persistence\Map\SpyCustomerTableMap;
use Spryker\Shared\Kernel\Store;
use Spryker\Shared\Library\Currency\CurrencyManager;
use SprykerEco\Shared\Klarna\KlarnaConstants;
use SprykerEco\Zed\Klarna\Business\Exception\NoShippingException;

/**
 * Class KlarnaCheckoutApi
 *
 * @package SprykerEco\Zed\Klarna\Business\Api\Handler
 *
 * @author Daniel Bohnhardt <daniel.bohnhardt@twt.de>
 */
class KlarnaCheckoutApi
{

    /**
     * @const int
     */
    const DEFAULT_TAX_RATE = 19;

    const CUSTOMER_GENDER_MALE = 'male';

    const CUSTOMER_GENDER_FEMALE = 'female';

    const CUSTOMER_TYPE = 'person';

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
    protected $confirmationUri;

    /**
     * @var string
     *
     * @author Daniel Bohnhardt <daniel.bohnhardt@twt.de>
     */
    protected $pushUri;

    /**
     * @var string
     *
     * @author Daniel Bohnhardt <daniel.bohnhardt@twt.de>
     */
    protected $termsUri;

    /**
     * @var string
     *
     * @author Daniel Bohnhardt <daniel.bohnhardt@twt.de>
     */
    protected $checkoutUri;

    /**
     * @var \Klarna_Checkout_ConnectorInterface
     *
     * @author Daniel Bohnhardt <daniel.bohnhardt@twt.de>
     */
    protected $connector;

    /**
     * KlarnaCheckoutApi constructor.
     *
     * @author Daniel Bohnhardt <daniel.bohnhardt@twt.de>
     *
     * @param string $merchantId
     * @param string $confirmationUri
     * @param string $pushUri
     * @param string $termsUri
     * @param string $checkoutUri
     * @param \Klarna_Checkout_ConnectorInterface $connector
     */
    public function __construct(
        $merchantId,
        $confirmationUri,
        $pushUri,
        $termsUri,
        $checkoutUri,
        $connector
    ) {
        $this->merchantId = $merchantId;

        $this->confirmationUri = $confirmationUri;
        $this->pushUri = $pushUri;
        $this->termsUri = $termsUri;
        $this->checkoutUri = $checkoutUri;
        $this->connector = $connector;
    }

    /**
     * @author Daniel Bohnhardt <daniel.bohnhardt@twt.de>
     *
     * @param \Generated\Shared\Transfer\QuoteTransfer $quoteTransfer
     *
     * @throws \SprykerEco\Zed\Klarna\Business\Exception\NoShippingException
     *
     * @return array
     */
    public function getCheckoutValues(QuoteTransfer $quoteTransfer)
    {
        $cart = $this->addArticles($quoteTransfer->getItems());

        $shipment = $quoteTransfer->getShipment();
        if ($shipment) {
            $shipmentMmethod = $shipment->getMethod();
            if ($shipmentMmethod) {
                $cart[] =
                    [
                        'type' => KlarnaConstants::SHIPPING_TYPE,
                        'reference' => (string)$shipmentMmethod->getIdShipmentMethod(),
                        'name' => $shipmentMmethod->getName(),
                        'quantity' => 1,
                        'unit_price' => $shipmentMmethod->getDefaultPrice(),
                        'tax_rate' => $shipmentMmethod->getTaxRate(),
                    ];
            }
        } else {
            throw new NoShippingException();
        }

        try {
            $return = [];

            $orderArray = $this->createOrderArray($quoteTransfer, $cart);
            $order = new Klarna_Checkout_Order($this->connector);
            $order->create($orderArray);

            $order->fetch();

            $return['orderid'] = $order['id'];

            $return['snippet'] = $order['gui']['snippet'];
        } catch (Exception $e) {
            $return['snippet'] = '';
            $return['orderid'] = '';
        }

        return $return;
    }

    /**
     * @author Daniel Bohnhardt <daniel.bohnhardt@twt.de>
     *
     * @param \Generated\Shared\Transfer\KlarnaCheckoutTransfer $klarnaCheckoutTransfer
     *
     * @return array
     */
    public function getSuccessValues(KlarnaCheckoutTransfer $klarnaCheckoutTransfer)
    {
        try {
            $order = $this->fetchKlarnaOrder($klarnaCheckoutTransfer);

            $return['orderid'] = $order['id'];

            $return['snippet'] = $order['gui']['snippet'];
        } catch (Exception $e) {
            $return['snippet'] = '';
            $return['orderid'] = '';
        }

        return $return;
    }

    /**
     * Mark checkout order as created.
     *
     * @author Daniel Bohnhardt <daniel.bohnhardt@twt.de>
     *
     * @param \Generated\Shared\Transfer\KlarnaCheckoutTransfer $klarnaCheckoutTransfer
     *
     * @return void
     */
    public function createOrder(KlarnaCheckoutTransfer $klarnaCheckoutTransfer)
    {
        try {
            $order = $this->fetchKlarnaOrder($klarnaCheckoutTransfer);

            if ($order['status'] === KlarnaConstants::STATUS_COMPLETE) {

                $update = [];
                $update['status'] = 'created';

                $order->update($update);
            }
        } catch (Exception $e) {

        }
    }

    /**
     * @author Daniel Bohnhardt <daniel.bohnhardt@twt.de>
     *
     * @param \Generated\Shared\Transfer\KlarnaCheckoutTransfer $klarnaCheckoutTransfer
     *
     * @return \Klarna_Checkout_Order
     */
    public function fetchKlarnaOrder(KlarnaCheckoutTransfer $klarnaCheckoutTransfer)
    {
        $order = new Klarna_Checkout_Order($this->connector, $klarnaCheckoutTransfer->getOrderid());
        $order->fetch();

        return $order;
    }

    /**
     * @author Daniel Bohnhardt <daniel.bohnhardt@twt.de>
     *
     * @param array $orderItems
     *
     * @return array
     */
    protected function addArticles($orderItems)
    {
        $cart = [];

        /** @var \Generated\Shared\Transfer\ItemTransfer $orderItem */
        foreach ($orderItems as $orderItem) {
            $cart[] = [
                'reference' => $orderItem->getSku(),
                'name' => $orderItem->getName(),
                'quantity' => $orderItem->getQuantity(),
                'unit_price' => $orderItem->getUnitGrossPrice(),
                'discount_rate' => $orderItem->getUnitTotalDiscountAmount(),
                'tax_rate' => ($orderItem->getTaxRate()) ?: self::DEFAULT_TAX_RATE,
            ];
        }

        return $cart;
    }

    /**
     * @return string
     */
    protected function getCurrency()
    {
        return CurrencyManager::getInstance()->getDefaultCurrency()->getIsoCode();
    }

    /**
     * @author Daniel Bohnhardt <daniel.bohnhardt@twt.de>
     *
     * @param \Generated\Shared\Transfer\QuoteTransfer $quoteTransfer
     *
     * @return array
     */
    protected function getCustomerData(QuoteTransfer $quoteTransfer)
    {
        /** @var \Generated\Shared\Transfer\CustomerTransfer $customer */
        $customer = $quoteTransfer->getCustomer();
        $customerData = [];

        if ($customer) {
            $defaultAddressId = $customer->getDefaultBillingAddress();
            if ($defaultAddressId) {
                $addresses = $customer->getAddresses()->getAddresses();
                /** @var \Generated\Shared\Transfer\AddressTransfer $address */
                foreach ($addresses as $address) {
                    if ($address->getIsDefaultBilling()) {
                        $defaultAddress = $address;
                        break;
                    }
                }
            }

            $customerData['address'] = [
                'email' => $customer->getEmail(),
                'given_name' => $customer->getFirstName(),
                'family_name' => $customer->getLastName(),
            ];

            if (isset($defaultAddress)) {
                $customerData['country'] = $defaultAddress->getIso2Code();
                ;
                $customerData['address']['street_name'] = ($defaultAddress->getAddress1()) ?: '';
                $customerData['address']['street_number'] = ($defaultAddress->getAddress2()) ?: '';
                $customerData['address']['postal_code'] = ($defaultAddress->getZipCode()) ?: '';
                $customerData['address']['city'] = ($defaultAddress->getCity()) ?: '';
                $customerData['address']['phone'] = ($defaultAddress->getPhone()) ?: '';
                $customerData['address']['title'] =
                    ($defaultAddress->getSalutation() == SpyCustomerTableMap::COL_SALUTATION_MR)
                    ? KlarnaConstants::CHECKOUT_API_MR
                    : KlarnaConstants::CHECKOUT_API_MRS;
            }

            $customerData['customer'] = [
                'type' => self::CUSTOMER_TYPE,
                'date_of_birth' => $customer->getDateOfBirth() ?: '',
            ];
        }

        return $customerData;
    }

    /**
     * @author Daniel Bohnhardt <daniel.bohnhardt@twt.de>
     *
     * @param \Generated\Shared\Transfer\QuoteTransfer $quoteTransfer
     * @param array $cart
     *
     * @return array
     */
    protected function createOrderArray(QuoteTransfer $quoteTransfer, array $cart)
    {
        $create = [];
        foreach ($cart as $item) {
            $create['cart']['items'][] = $item;
        }

        $store = Store::getInstance();

        $customerData = $this->getCustomerData($quoteTransfer);
        if (count($customerData)) {
            $create['shipping_address'] = $customerData['address'];
            $create['customer'] = $customerData['customer'];
            $create['purchase_country'] = $customerData['country'];
        }

        $create['purchase_currency'] = $this->getCurrency();
        $create['locale'] = str_replace('_', '-', $store->getCurrentLocale());

        // $create['recurring'] = true;
        $create['merchant'] = [
            'id' => $this->merchantId,
            'terms_uri' => $this->termsUri,
            'checkout_uri' => $this->checkoutUri,
            'confirmation_uri' => $this->confirmationUri .
                '?klarna_order_id={checkout.order.id}',
            'push_uri' => $this->pushUri .
                '?klarna_order_id={checkout.order.id}',
        ];

        return $create;
    }

}
