<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Zed\Klarna\Business\Request;

use Generated\Shared\Transfer\AddressTransfer;
use Generated\Shared\Transfer\CustomerTransfer;
use Generated\Shared\Transfer\ExpenseTransfer;
use Generated\Shared\Transfer\ItemTransfer;
use Generated\Shared\Transfer\KlarnaCheckoutTransfer;
use Generated\Shared\Transfer\KlarnaPaymentTransfer;
use Generated\Shared\Transfer\PaymentTransfer;
use Generated\Shared\Transfer\QuoteTransfer;
use Generated\Shared\Transfer\ShipmentMethodTransfer;
use Generated\Shared\Transfer\ShipmentTransfer;
use Generated\Shared\Transfer\TotalsTransfer;
use Orm\Zed\Customer\Persistence\Map\SpyCustomerTableMap;
use Spryker\Shared\Klarna\KlarnaConstants;
use Spryker\Shared\Shipment\ShipmentConstants;
use Spryker\Zed\Klarna\Business\Api\Handler\KlarnaCheckoutApi;
use Spryker\Zed\Klarna\Business\Exception\NoShippingException;
use Spryker\Zed\Klarna\Dependency\Facade\KlarnaToCheckoutBridgeInterface;

/**
 * Class KlarnaCheckout
 *
 * @package Spryker\Zed\Klarna\Business\Payment
 *
 * @author Daniel Bohnhardt <daniel.bohnhardt@twt.de>
 */
class KlarnaCheckout
{

    /**
     * @var \Spryker\Zed\Klarna\Business\Api\Handler\KlarnaCheckoutApi
     *
     * @author Daniel Bohnhardt <daniel.bohnhardt@twt.de>
     */
    protected $klarnaCheckoutApi;

    /**
     * @var \Spryker\Zed\Klarna\Dependency\Facade\KlarnaToCheckoutBridgeInterface
     *
     * @author Daniel Bohnhardt <daniel.bohnhardt@twt.de>
     */
    protected $checkoutFacade;

    /**
     * KlarnaCheckout constructor.
     *
     * @param \Spryker\Zed\Klarna\Business\Api\Handler\KlarnaCheckoutApi $klarnaCheckoutApi
     * @param \Spryker\Zed\Klarna\Dependency\Facade\KlarnaToCheckoutBridgeInterface $checkoutFacade
     *
     * @author Daniel Bohnhardt <daniel.bohnhardt@twt.de>
     */
    public function __construct(
        KlarnaCheckoutApi $klarnaCheckoutApi,
        KlarnaToCheckoutBridgeInterface $checkoutFacade
    ) {
        $this->klarnaCheckoutApi = $klarnaCheckoutApi;
        $this->checkoutFacade    = $checkoutFacade;
    }

    /**
     * @param \Generated\Shared\Transfer\QuoteTransfer $quoteTransfer
     *
     * @return \Generated\Shared\Transfer\KlarnaCheckoutTransfer
     * @author Daniel Bohnhardt <daniel.bohnhardt@twt.de>
     */
    public function getCheckoutHtml(QuoteTransfer $quoteTransfer)
    {
        try {
            $return = $this->klarnaCheckoutApi->getCheckoutValues($quoteTransfer);
        } catch (NoShippingException $exception) {
            $return = ['snippet' => '', 'orderid' => ''];
        }

        $transfer = new KlarnaCheckoutTransfer();
        $transfer->setHtml($return['snippet']);
        $transfer->setOrderid($return['orderid']);

        return $transfer;
    }

    /**
     * @param \Generated\Shared\Transfer\KlarnaCheckoutTransfer $klarnaCheckoutTransfer
     *
     * @return \Generated\Shared\Transfer\KlarnaCheckoutTransfer
     * @author Daniel Bohnhardt <daniel.bohnhardt@twt.de>
     */
    public function getSuccessHtml(KlarnaCheckoutTransfer $klarnaCheckoutTransfer)
    {
        $return = $this->klarnaCheckoutApi->getSuccessValues($klarnaCheckoutTransfer);

        $transfer = new KlarnaCheckoutTransfer();
        $transfer->setHtml($return['snippet']);
        $transfer->setOrderid($return['orderid']);

        return $transfer;
    }

    /**
     * @param \Generated\Shared\Transfer\KlarnaCheckoutTransfer $klarnaCheckoutTransfer
     *
     * @return bool
     * @author Daniel Bohnhardt <daniel.bohnhardt@twt.de>
     */
    public function createCheckoutOrder(KlarnaCheckoutTransfer $klarnaCheckoutTransfer)
    {
        $order = $this->klarnaCheckoutApi->fetchKlarnaOrder($klarnaCheckoutTransfer);
        if ($order['status'] === KlarnaConstants::STATUS_COMPLETE) {
            $quoteTransfer = new QuoteTransfer();
            $shippingItem  = $this->addCartItemsToQuote($order, $quoteTransfer);
            $this->addShippingToQuote($shippingItem, $quoteTransfer);

            $billingTransfer = $this->addCustomerToQuote($order, $quoteTransfer);

            $this->addPaymentToQuote($order, $billingTransfer, $quoteTransfer);

            $this->addTotalsToQuote($order, $quoteTransfer);

            $return = $this->checkoutFacade->placeOrder($quoteTransfer);
            if (count($return->getErrors())) {
                return false;
            }

            $this->klarnaCheckoutApi->createOrder($klarnaCheckoutTransfer);
        }

        return true;
    }

    /**
     * @param array $shippingItem
     * @param \Generated\Shared\Transfer\QuoteTransfer $quoteTransfer
     *
     * @return void
     * @author Daniel Bohnhardt <daniel.bohnhardt@twt.de>
     */
    protected function addShippingToQuote(array $shippingItem, QuoteTransfer $quoteTransfer)
    {
        if (count($shippingItem)) {
            $shipmentMethodTransfer = new ShipmentMethodTransfer();
            $shipmentMethodTransfer->setName($shippingItem['name']);
            $shipmentMethodTransfer->setDefaultPrice($shippingItem['unit_price']);
            $shipmentMethodTransfer->setIdShipmentMethod($shippingItem['reference']);

            $shipmentTransfer = new ShipmentTransfer();
            $shipmentTransfer->setMethod($shipmentMethodTransfer);

            $shipmentExpenseTransfer = new ExpenseTransfer();
            $shipmentExpenseTransfer->fromArray($shipmentMethodTransfer->toArray(), true);
            $shipmentExpenseTransfer->setType(ShipmentConstants::SHIPMENT_EXPENSE_TYPE);
            $shipmentExpenseTransfer->setUnitGrossPrice($shipmentMethodTransfer->getDefaultPrice());
            $shipmentExpenseTransfer->setQuantity(1);
            $quoteTransfer->addExpense($shipmentExpenseTransfer);

            $quoteTransfer->setShipment($shipmentTransfer);
        }
    }

    /**
     * @param \Klarna_Checkout_Order $order
     * @param \Generated\Shared\Transfer\AddressTransfer $billingTransfer
     * @param \Generated\Shared\Transfer\QuoteTransfer $quoteTransfer
     *
     * @return void
     * @author Daniel Bohnhardt <daniel.bohnhardt@twt.de>
     */
    protected function addPaymentToQuote(
        \Klarna_Checkout_Order $order,
        AddressTransfer $billingTransfer,
        QuoteTransfer $quoteTransfer
    ) {
        $paymentTransfer = new PaymentTransfer();
        $paymentTransfer->setPaymentMethod(KlarnaConstants::BRAND_CHECKOUT)
                        ->setPaymentProvider(KlarnaConstants::KLARNA)
                        ->setPaymentSelection(KlarnaConstants::CHECKOUT_PAYMENT_METHOD);
        $billingAddress = $order['billing_address'];
        $klarnaPaymentTransfer = new KlarnaPaymentTransfer();
        $klarnaPaymentTransfer->setEmail($billingAddress['email'])
                              ->setDateOfBirth($order['customer']['date_of_birth'])
                              ->setGender(ucfirst($order['customer']['gender']))
                              ->setLanguageIso2Code($order['purchase_country'])
                              ->setCurrencyIso3Code($order['purchase_currency'])
                              ->setPreCheckId($order['reservation'])
                              ->setClientIp($quoteTransfer->getClientIp())
                              ->setAccountBrand(KlarnaConstants::BRAND_CHECKOUT)
                              ->setAddress($billingTransfer);

        if (isset($billingAddress['phone'])) {
            $klarnaPaymentTransfer->setPhone($billingAddress['phone']);
        }
        $paymentTransfer->setKlarna($klarnaPaymentTransfer);
        $quoteTransfer->setPayment($paymentTransfer);
    }

    /**
     * @param \Klarna_Checkout_Order $order
     * @param \Generated\Shared\Transfer\QuoteTransfer $quoteTransfer
     *
     * @return void
     * @author Daniel Bohnhardt <daniel.bohnhardt@twt.de>
     */
    protected function addTotalsToQuote(\Klarna_Checkout_Order $order, QuoteTransfer $quoteTransfer)
    {
        $totalsTransfer = new TotalsTransfer();
        $totalsTransfer->setGrandTotal($order['cart']['total_price_including_tax']);

        $quoteTransfer->setTotals($totalsTransfer);
    }

    /**
     * @param array $shippingAddress
     * @param \Generated\Shared\Transfer\QuoteTransfer $quoteTransfer
     *
     * @return \Generated\Shared\Transfer\AddressTransfer
     * @author Daniel Bohnhardt <daniel.bohnhardt@twt.de>
     */
    protected function addShippingAddressToQuote(array $shippingAddress, QuoteTransfer $quoteTransfer)
    {
        $shippingTransfer = new AddressTransfer();
        $shippingTransfer->setLastName($shippingAddress['family_name'])
                         ->setFirstName($shippingAddress['given_name'])
                         ->setEmail($shippingAddress['email'])
                         ->setCity($shippingAddress['city'])
                         ->setZipCode($shippingAddress['postal_code'])
                         ->setAddress1($shippingAddress['street_name'])
                         ->setAddress2($shippingAddress['street_number'])
                         ->setSalutation(($shippingAddress['title'] === KlarnaConstants::CHECKOUT_API_MR)
                                            ? SpyCustomerTableMap::COL_SALUTATION_MR
                                            : SpyCustomerTableMap::COL_SALUTATION_MRS)
                         ->setIso2Code(strtoupper($shippingAddress['country']));

        if (isset($shippingAddress['phone'])) {
            $shippingTransfer->setPhone($shippingAddress['phone']);
        }

        $quoteTransfer->setShippingAddress($shippingTransfer);

        return $shippingTransfer;
    }

    /**
     * @param \Klarna_Checkout_Order $order
     * @param \Generated\Shared\Transfer\QuoteTransfer $quoteTransfer
     *
     * @return \Generated\Shared\Transfer\AddressTransfer
     * @author Daniel Bohnhardt <daniel.bohnhardt@twt.de>
     */
    protected function addCustomerToQuote(\Klarna_Checkout_Order $order, QuoteTransfer $quoteTransfer)
    {
        $billingAddress   = $order['billing_address'];
        $customerTransfer = new CustomerTransfer();
        $customerTransfer->setFirstName($billingAddress['given_name'])
                         ->setLastName($billingAddress['family_name'])
                         ->setDateOfBirth($order['customer']['date_of_birth'])
                         ->setSalutation(($billingAddress['title'] === KlarnaConstants::CHECKOUT_API_MR)
                                            ? SpyCustomerTableMap::COL_SALUTATION_MR
                                            : SpyCustomerTableMap::COL_SALUTATION_MRS)
                         ->setEmail($billingAddress['email'])
                         ->setIsGuest(true);

        $billingTransfer = $this->addBillingAddressToQuote($quoteTransfer, $billingAddress);

        $shippingTransfer = $this->addShippingAddressToQuote($order['shipping_address'], $quoteTransfer);
        $customerTransfer->setBillingAddress(new \ArrayObject($billingTransfer));
        $customerTransfer->setShippingAddress(new \ArrayObject($shippingTransfer));

        $quoteTransfer->setCustomer($customerTransfer);

        return $billingTransfer;
    }

    /**
     * @param \Generated\Shared\Transfer\QuoteTransfer $quoteTransfer
     * @param array $billingAddress
     *
     * @return \Generated\Shared\Transfer\AddressTransfer
     * @author Daniel Bohnhardt <daniel.bohnhardt@twt.de>
     */
    protected function addBillingAddressToQuote(QuoteTransfer $quoteTransfer, array $billingAddress)
    {
        $billingTransfer = new AddressTransfer();
        $billingTransfer->setLastName($billingAddress['family_name'])
                        ->setFirstName($billingAddress['given_name'])
                        ->setEmail($billingAddress['email'])
                        ->setCity($billingAddress['city'])
                        ->setZipCode($billingAddress['postal_code'])
                        ->setAddress1($billingAddress['street_name'])
                        ->setAddress2($billingAddress['street_number'])
                        ->setSalutation(($billingAddress['title'] === KlarnaConstants::CHECKOUT_API_MR)
                                            ? SpyCustomerTableMap::COL_SALUTATION_MR
                                            : SpyCustomerTableMap::COL_SALUTATION_MRS)
                        ->setIso2Code(strtoupper($billingAddress['country']));

        if (isset($billingAddress['phone'])) {
            $billingTransfer->setPhone($billingAddress['phone']);
        }

        $quoteTransfer->setBillingAddress($billingTransfer);

        return $billingTransfer;
    }

    /**
     * @param \Klarna_Checkout_Order $order
     * @param \Generated\Shared\Transfer\QuoteTransfer $quoteTransfer
     *
     * @return array
     * @author Daniel Bohnhardt <daniel.bohnhardt@twt.de>
     */
    protected function addCartItemsToQuote(\Klarna_Checkout_Order $order, QuoteTransfer $quoteTransfer)
    {
        $cartItems    = new \ArrayObject();
        $shippingItem = [];

        foreach ($order['cart']['items'] as $item) {
            if ($item['type'] === KlarnaConstants::SHIPPING_TYPE) {
                $shippingItem = $item;
            } else {
                $transferItem = new ItemTransfer();
                $transferItem->setSku($item['reference'])
                             ->setName($item['name'])
                             ->setQuantity($item['quantity'])
                             ->setUnitGrossPrice($item['unit_price'])
                             ->setUnitTotalDiscountAmount($item['discount_rate'])
                             ->setTaxRate($item['tax_rate']);
                $cartItems->append($transferItem);
            }
        }
        $quoteTransfer->setItems($cartItems);

        return $shippingItem;
    }

}
