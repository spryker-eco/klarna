<?php

/**
 * MIT License
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace SprykerEco\Zed\Klarna\Business\Request;

use ArrayObject;
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
use Klarna_Checkout_Order;
use Orm\Zed\Customer\Persistence\Map\SpyCustomerTableMap;
use Spryker\Shared\Shipment\ShipmentConstants;
use SprykerEco\Shared\Klarna\KlarnaConfig;
use SprykerEco\Zed\Klarna\Business\Api\Handler\KlarnaCheckoutApi;
use SprykerEco\Zed\Klarna\Business\Api\Handler\KlarnaCheckoutApiInterface;
use SprykerEco\Zed\Klarna\Business\Exception\NoShippingException;
use SprykerEco\Zed\Klarna\Dependency\Facade\KlarnaToCheckoutBridgeInterface;

/**
 * Class KlarnaCheckout
 *
 * @package SprykerEco\Zed\Klarna\Business\Payment
 *
 * @author Daniel Bohnhardt <daniel.bohnhardt@twt.de>
 */
class KlarnaCheckout implements KlarnaCheckoutInterface
{

    const SNIPPET = 'snippet';
    const ORDERID = 'orderid';
    const PHONE = 'phone';
    const GIVEN_NAME = 'given_name';
    const FAMILY_NAME = 'family_name';
    const CUSTOMER = 'customer';
    const DATE_OF_BIRTH = 'date_of_birth';
    const TITLE = 'title';
    const EMAIL = 'email';
    const SHIPPING_ADDRESS = 'shipping_address';
    const BILLING_ADDRESS = 'billing_address';
    const CITY = 'city';
    const POSTAL_CODE = 'postal_code';
    const STREET_NAME = 'street_name';
    const STREET_NUMBER = 'street_number';
    const COUNTRY = 'country';
    const CART = 'cart';
    const ITEMS = 'items';
    const TYPE = 'type';
    const REFERENCE = 'reference';
    const NAME = 'name';
    const QUANTITY = 'quantity';
    const UNIT_PRICE = 'unit_price';
    const TAX_RATE = 'tax_rate';
    const DISCOUNT_RATE = 'discount_rate';
    /**
     * @var \SprykerEco\Zed\Klarna\Business\Api\Handler\KlarnaCheckoutApiInterface
     *
     * @author Daniel Bohnhardt <daniel.bohnhardt@twt.de>
     */
    protected $klarnaCheckoutApi;

    /**
     * @var \SprykerEco\Zed\Klarna\Dependency\Facade\KlarnaToCheckoutBridgeInterface
     *
     * @author Daniel Bohnhardt <daniel.bohnhardt@twt.de>
     */
    protected $checkoutFacade;

    /**
     * KlarnaCheckout constructor.
     *
     * @author Daniel Bohnhardt <daniel.bohnhardt@twt.de>
     *
     * @param \SprykerEco\Zed\Klarna\Business\Api\Handler\KlarnaCheckoutApiInterface $klarnaCheckoutApi
     * @param \SprykerEco\Zed\Klarna\Dependency\Facade\KlarnaToCheckoutBridgeInterface $checkoutFacade
     */
    public function __construct(
        KlarnaCheckoutApiInterface $klarnaCheckoutApi,
        KlarnaToCheckoutBridgeInterface $checkoutFacade
    ) {
        $this->klarnaCheckoutApi = $klarnaCheckoutApi;
        $this->checkoutFacade = $checkoutFacade;
    }

    /**
     * @author Daniel Bohnhardt <daniel.bohnhardt@twt.de>
     *
     * @param \Generated\Shared\Transfer\QuoteTransfer $quoteTransfer
     *
     * @return \Generated\Shared\Transfer\KlarnaCheckoutTransfer
     */
    public function getCheckoutHtml(QuoteTransfer $quoteTransfer)
    {
        try {
            $return = $this->klarnaCheckoutApi->getCheckoutValues($quoteTransfer);
        } catch (NoShippingException $exception) {
            $return = [self::SNIPPET => '', self::ORDERID => ''];
        }

        $transfer = new KlarnaCheckoutTransfer();
        $transfer->setHtml($return[self::SNIPPET]);
        $transfer->setOrderid($return[self::ORDERID]);

        return $transfer;
    }

    /**
     * @author Daniel Bohnhardt <daniel.bohnhardt@twt.de>
     *
     * @param \Generated\Shared\Transfer\KlarnaCheckoutTransfer $klarnaCheckoutTransfer
     *
     * @return \Generated\Shared\Transfer\KlarnaCheckoutTransfer
     */
    public function getSuccessHtml(KlarnaCheckoutTransfer $klarnaCheckoutTransfer)
    {
        $return = $this->klarnaCheckoutApi->getSuccessValues($klarnaCheckoutTransfer);

        $transfer = new KlarnaCheckoutTransfer();
        $transfer->setHtml($return[self::SNIPPET]);
        $transfer->setOrderid($return[self::ORDERID]);

        return $transfer;
    }

    /**
     * @author Daniel Bohnhardt <daniel.bohnhardt@twt.de>
     *
     * @param \Generated\Shared\Transfer\KlarnaCheckoutTransfer $klarnaCheckoutTransfer
     *
     * @return bool
     */
    public function createCheckoutOrder(KlarnaCheckoutTransfer $klarnaCheckoutTransfer)
    {
        $order = $this->klarnaCheckoutApi->fetchKlarnaOrder($klarnaCheckoutTransfer);
        if ($order['status'] === KlarnaConfig::STATUS_COMPLETE) {
            $quoteTransfer = new QuoteTransfer();
            $shippingItem = $this->addCartItemsToQuote($order, $quoteTransfer);
            $this->addShippingToQuote($shippingItem, $quoteTransfer);

            $billingTransfer = $this->addCustomerToQuote($order, $quoteTransfer);

            $this->addPaymentToQuote($order, $billingTransfer, $quoteTransfer);

            $this->addTotalsToQuote($order, $quoteTransfer);

            $return = $this->checkoutFacade->placeOrder($quoteTransfer);
            if ($return->getErrors()->count() > 0) {
                return false;
            }

            $this->klarnaCheckoutApi->createOrder($klarnaCheckoutTransfer);
        }

        return true;
    }

    /**
     * @author Daniel Bohnhardt <daniel.bohnhardt@twt.de>
     *
     * @param array $shippingItem
     * @param \Generated\Shared\Transfer\QuoteTransfer $quoteTransfer
     *
     * @return void
     */
    protected function addShippingToQuote(array $shippingItem, QuoteTransfer $quoteTransfer)
    {
        if (count($shippingItem)) {
            $shipmentMethodTransfer = new ShipmentMethodTransfer();
            $shipmentMethodTransfer->setName($shippingItem[self::NAME]);
            $shipmentMethodTransfer->setDefaultPrice($shippingItem[self::UNIT_PRICE]);
            $shipmentMethodTransfer->setIdShipmentMethod($shippingItem[self::REFERENCE]);

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
     * @author Daniel Bohnhardt <daniel.bohnhardt@twt.de>
     *
     * @param \Klarna_Checkout_Order $order
     * @param \Generated\Shared\Transfer\AddressTransfer $billingTransfer
     * @param \Generated\Shared\Transfer\QuoteTransfer $quoteTransfer
     *
     * @return void
     */
    protected function addPaymentToQuote(
        Klarna_Checkout_Order $order,
        AddressTransfer $billingTransfer,
        QuoteTransfer $quoteTransfer
    ) {
        $paymentTransfer = new PaymentTransfer();
        $paymentTransfer->setPaymentMethod(KlarnaConfig::BRAND_CHECKOUT)
                        ->setPaymentProvider(KlarnaConfig::PROVIDER_NAME)
                        ->setPaymentSelection(KlarnaConfig::CHECKOUT_PAYMENT_METHOD);
        $billingAddress = $order[self::BILLING_ADDRESS];
        $klarnaPaymentTransfer = new KlarnaPaymentTransfer();
        $klarnaPaymentTransfer->setEmail($billingAddress[self::EMAIL])
                              ->setDateOfBirth($order[self::CUSTOMER][self::DATE_OF_BIRTH])
                              ->setGender(ucfirst($order[self::CUSTOMER]['gender']))
                              ->setLanguageIso2Code($order['purchase_country'])
                              ->setCurrencyIso3Code($order['purchase_currency'])
                              ->setPreCheckId($order['reservation'])
                              ->setClientIp($quoteTransfer->getClientIp())
                              ->setAccountBrand(KlarnaConfig::BRAND_CHECKOUT)
                              ->setAddress($billingTransfer);

        if (isset($billingAddress[self::PHONE])) {
            $klarnaPaymentTransfer->setPhone($billingAddress[self::PHONE]);
        }
        $paymentTransfer->setKlarna($klarnaPaymentTransfer);
        $quoteTransfer->setPayment($paymentTransfer);
    }

    /**
     * @author Daniel Bohnhardt <daniel.bohnhardt@twt.de>
     *
     * @param \Klarna_Checkout_Order $order
     * @param \Generated\Shared\Transfer\QuoteTransfer $quoteTransfer
     *
     * @return void
     */
    protected function addTotalsToQuote(Klarna_Checkout_Order $order, QuoteTransfer $quoteTransfer)
    {
        $totalsTransfer = new TotalsTransfer();
        $totalsTransfer->setGrandTotal($order[self::CART]['total_price_including_tax']);

        $quoteTransfer->setTotals($totalsTransfer);
    }

    /**
     * @author Daniel Bohnhardt <daniel.bohnhardt@twt.de>
     *
     * @param array $shippingAddress
     * @param \Generated\Shared\Transfer\QuoteTransfer $quoteTransfer
     *
     * @return \Generated\Shared\Transfer\AddressTransfer
     */
    protected function addShippingAddressToQuote(array $shippingAddress, QuoteTransfer $quoteTransfer)
    {
        $shippingTransfer = new AddressTransfer();
        $shippingTransfer->setLastName($shippingAddress[self::FAMILY_NAME])
                         ->setFirstName($shippingAddress[self::GIVEN_NAME])
                         ->setEmail($shippingAddress[self::EMAIL])
                         ->setCity($shippingAddress[self::CITY])
                         ->setZipCode($shippingAddress[self::POSTAL_CODE])
                         ->setAddress1($shippingAddress[self::STREET_NAME])
                         ->setAddress2($shippingAddress[self::STREET_NUMBER])
                         ->setSalutation(($shippingAddress[self::TITLE] === KlarnaConfig::CHECKOUT_API_MR)
                                            ? SpyCustomerTableMap::COL_SALUTATION_MR
                                            : SpyCustomerTableMap::COL_SALUTATION_MRS)
                         ->setIso2Code(strtoupper($shippingAddress[self::COUNTRY]));

        if (isset($shippingAddress[self::PHONE])) {
            $shippingTransfer->setPhone($shippingAddress[self::PHONE]);
        }

        $quoteTransfer->setShippingAddress($shippingTransfer);

        return $shippingTransfer;
    }

    /**
     * @author Daniel Bohnhardt <daniel.bohnhardt@twt.de>
     *
     * @param \Klarna_Checkout_Order $order
     * @param \Generated\Shared\Transfer\QuoteTransfer $quoteTransfer
     *
     * @return \Generated\Shared\Transfer\AddressTransfer
     */
    protected function addCustomerToQuote(Klarna_Checkout_Order $order, QuoteTransfer $quoteTransfer)
    {
        $billingAddress = $order[self::BILLING_ADDRESS];
        $customerTransfer = new CustomerTransfer();
        $customerTransfer->setFirstName($billingAddress[self::GIVEN_NAME])
                         ->setLastName($billingAddress[self::FAMILY_NAME])
                         ->setDateOfBirth($order[self::CUSTOMER][self::DATE_OF_BIRTH])
                         ->setSalutation(($billingAddress[self::TITLE] === KlarnaConfig::CHECKOUT_API_MR)
                                            ? SpyCustomerTableMap::COL_SALUTATION_MR
                                            : SpyCustomerTableMap::COL_SALUTATION_MRS)
                         ->setEmail($billingAddress[self::EMAIL])
                         ->setIsGuest(true);

        $billingTransfer = $this->addBillingAddressToQuote($quoteTransfer, $billingAddress);

        $shippingTransfer = $this->addShippingAddressToQuote($order[self::SHIPPING_ADDRESS], $quoteTransfer);
        $customerTransfer->setBillingAddress(new ArrayObject($billingTransfer));
        $customerTransfer->setShippingAddress(new ArrayObject($shippingTransfer));

        $quoteTransfer->setCustomer($customerTransfer);

        return $billingTransfer;
    }

    /**
     * @author Daniel Bohnhardt <daniel.bohnhardt@twt.de>
     *
     * @param \Generated\Shared\Transfer\QuoteTransfer $quoteTransfer
     * @param array $billingAddress
     *
     * @return \Generated\Shared\Transfer\AddressTransfer
     */
    protected function addBillingAddressToQuote(QuoteTransfer $quoteTransfer, array $billingAddress)
    {
        $billingTransfer = new AddressTransfer();
        $billingTransfer->setLastName($billingAddress[self::FAMILY_NAME])
                        ->setFirstName($billingAddress[self::GIVEN_NAME])
                        ->setEmail($billingAddress[self::EMAIL])
                        ->setCity($billingAddress[self::CITY])
                        ->setZipCode($billingAddress[self::POSTAL_CODE])
                        ->setAddress1($billingAddress[self::STREET_NAME])
                        ->setAddress2($billingAddress[self::STREET_NUMBER])
                        ->setSalutation(($billingAddress[self::TITLE] === KlarnaConfig::CHECKOUT_API_MR)
                                            ? SpyCustomerTableMap::COL_SALUTATION_MR
                                            : SpyCustomerTableMap::COL_SALUTATION_MRS)
                        ->setIso2Code(strtoupper($billingAddress[self::COUNTRY]));

        if (isset($billingAddress[self::PHONE])) {
            $billingTransfer->setPhone($billingAddress[self::PHONE]);
        }

        $quoteTransfer->setBillingAddress($billingTransfer);

        return $billingTransfer;
    }

    /**
     * @author Daniel Bohnhardt <daniel.bohnhardt@twt.de>
     *
     * @param \Klarna_Checkout_Order $order
     * @param \Generated\Shared\Transfer\QuoteTransfer $quoteTransfer
     *
     * @return array
     */
    protected function addCartItemsToQuote(Klarna_Checkout_Order $order, QuoteTransfer $quoteTransfer)
    {
        $cartItems = new ArrayObject();
        $shippingItem = [];

        foreach ($order[self::CART][self::ITEMS] as $item) {
            if ($item[self::TYPE] === KlarnaConfig::SHIPPING_TYPE) {
                $shippingItem = $item;
            } else {
                $transferItem = new ItemTransfer();
                $transferItem->setSku($item[self::REFERENCE])
                             ->setName($item[self::NAME])
                             ->setQuantity($item[self::QUANTITY])
                             ->setUnitGrossPrice($item[self::UNIT_PRICE])
                             ->setUnitDiscountAmountAggregation($item[self::DISCOUNT_RATE])
                             ->setTaxRate($item[self::TAX_RATE]);
                $cartItems->append($transferItem);
            }
        }
        $quoteTransfer->setItems($cartItems);

        return $shippingItem;
    }
}
