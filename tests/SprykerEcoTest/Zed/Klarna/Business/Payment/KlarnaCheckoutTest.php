<?php

/**
 * MIT License
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace SprykerEcoTest\Zed\Klarna\Business\Payment;

use Codeception\TestCase\Test;
use Generated\Shared\Transfer\CheckoutResponseTransfer;
use Generated\Shared\Transfer\KlarnaCheckoutTransfer;
use Generated\Shared\Transfer\QuoteTransfer;
use Klarna_Checkout_Order;
use SprykerEco\Shared\Klarna\KlarnaConstants;
use SprykerEco\Zed\Klarna\Business\Api\Handler\KlarnaCheckoutApi;
use SprykerEco\Zed\Klarna\Business\Exception\NoShippingException;
use SprykerEco\Zed\Klarna\Business\Request\KlarnaCheckout;

class KlarnaCheckoutTest extends Test
{
    /**
     * @return void
     */
    public function testGetCheckoutHtml()
    {
        $quoteTransfer = new QuoteTransfer();

        $klarnaCheckout = $this->getKlarnaCheckoutObject();
        $result = $klarnaCheckout->getCheckoutHtml($quoteTransfer);

        $this->assertInstanceOf('\Generated\Shared\Transfer\KlarnaCheckoutTransfer', $result);
        $this->assertSame('testSnippet', $result->getHtml());
        $this->assertSame('testOrderId', $result->getOrderid());
    }

    /**
     * @return void
     */
    public function testGetCheckoutHtmlFail()
    {
        $quoteTransfer = new QuoteTransfer();

        $klarnaCheckout = $this->getKlarnaCheckoutObject(true);
        $result = $klarnaCheckout->getCheckoutHtml($quoteTransfer);

        $this->assertInstanceOf('\Generated\Shared\Transfer\KlarnaCheckoutTransfer', $result);
        $this->assertSame('', $result->getHtml());
        $this->assertSame('', $result->getOrderid());
    }

    /**
     * @return void
     */
    public function testGetSuccessHtml()
    {
        $klarnaCheckoutTransfer = new KlarnaCheckoutTransfer();
        $klarnaCheckoutTransfer->setOrderid('12345');

        $klarnaCheckout = $this->getKlarnaCheckoutObject();
        $result = $klarnaCheckout->getSuccessHtml($klarnaCheckoutTransfer);

        $this->assertInstanceOf('\Generated\Shared\Transfer\KlarnaCheckoutTransfer', $result);
        $this->assertSame('testSuccessSnippet', $result->getHtml());
        $this->assertSame('testSuccessOrderId', $result->getOrderid());
    }

    /**
     * @return void
     */
    public function testGetSuccessHtmlFail()
    {
        $klarnaCheckoutTransfer = new KlarnaCheckoutTransfer();
        $klarnaCheckoutTransfer->setOrderid('12345');

        $klarnaCheckout = $this->getKlarnaCheckoutObject(true);
        $result = $klarnaCheckout->getSuccessHtml($klarnaCheckoutTransfer);

        $this->assertInstanceOf('\Generated\Shared\Transfer\KlarnaCheckoutTransfer', $result);
        $this->assertSame('', $result->getHtml());
        $this->assertSame('', $result->getOrderid());
    }

    /**
     * @return void
     */
    public function testCreateCheckoutOrder()
    {
        $klarnaCheckoutTransfer = new KlarnaCheckoutTransfer();
        $klarnaCheckoutTransfer->setOrderid('12345');

        $klarnaCheckout = $this->getKlarnaCheckoutCreateOrderObject(true);
        $result = $klarnaCheckout->createCheckoutOrder($klarnaCheckoutTransfer);

        $this->assertTrue($result);
    }

    /**
     * @param bool $returnUpdateError
     *
     * @return \SprykerEco\Zed\Klarna\Business\Request\KlarnaCheckout
     */
    protected function getKlarnaCheckoutCreateOrderObject($returnUpdateError = false)
    {
        $checkoutFacadeMock = $this->getMockBuilder(
            'SprykerEco\Zed\Klarna\Dependency\Facade\KlarnaToCheckoutBridgeInterface'
        )->setMethods(['placeOrder'])
        ->getMock();
        $checkoutResponseTransfer = new CheckoutResponseTransfer();
        $checkoutFacadeMock->expects($this->any())->method('placeOrder')->willReturn($checkoutResponseTransfer);

        $klarnaCheckoutApiMock = $this->getMockBuilder(KlarnaCheckoutApi::class)
            ->disableOriginalConstructor()
            ->setMethods(['createOrder', 'fetchKlarnaOrder'])
            ->getMock();

        $fetchKlarnaOrderReturnData = [
            'status' => KlarnaConstants::STATUS_COMPLETE,
            'cart' => [
                'items' => [
                    [
                        'type' => KlarnaConstants::SHIPPING_TYPE,
                        'reference' => '',
                        'name' => 'shipping',
                        'quantity' => 1,
                        'unit_price' => 4.95,
                        'discount_rate' => 0,
                        'tax_rate' => 19,
                    ],
                    [
                        'type' => '',
                        'reference' => '',
                        'name' => 'Artikel',
                        'quantity' => 1,
                        'unit_price' => 9.95,
                        'discount_rate' => 0,
                        'tax_rate' => 19,
                    ],
                ],
                'total_price_including_tax' => 14.90,
            ],
            'billing_address' => [
                'given_name' => 'testperson',
                'family_name' => 'lastname',
                'email' => 'test@test.de',
                'city' => 'testcity',
                'street_name' => 'teststreet',
                'street_number' => 'teststreetnumber',
                'title' => 'Herr',
                'country' => 'DE',
                'postal_code' => '41460',

            ],
            'shipping_address' => [
                'given_name' => 'testperson',
                'family_name' => 'lastname',
                'email' => 'test@test.de',
                'city' => 'testcity',
                'street_name' => 'teststreet',
                'street_number' => 'teststreetnumber',
                'title' => 'Herr',
                'country' => 'DE',
                'postal_code' => '41460',
            ],
            'customer' => [
                'date_of_birth' => '',
                'gender' => 'Herr',
            ],
            'purchase_country' => 'DE',
            'purchase_currency' => 'EUR',
            'reservation' => 'reservationId',

        ];

        $connector = $this->createMock('\Klarna_Checkout_ConnectorInterface');
        $fetchKlarnaOrderReturn = new Klarna_Checkout_Order($connector);
        $fetchKlarnaOrderReturn->parse($fetchKlarnaOrderReturnData);

        $klarnaCheckoutApiMock
            ->expects($this->any())
            ->method('fetchKlarnaOrder')
            ->willReturn($fetchKlarnaOrderReturn);

        return new KlarnaCheckout($klarnaCheckoutApiMock, $checkoutFacadeMock);
    }

    /**
     * @param bool $returnUpdateError
     *
     * @return \SprykerEco\Zed\Klarna\Business\Request\KlarnaCheckout
     */
    protected function getKlarnaCheckoutObject($returnUpdateError = false)
    {
        $checkoutFacadeMock = $this->getMockBuilder(
            'SprykerEco\Zed\Klarna\Dependency\Facade\KlarnaToCheckoutBridgeInterface'
        )->setMethods(['placeOrder'])
        ->getMock();

        $klarnaCheckoutApiMock = $this->getMockBuilder(KlarnaCheckoutApi::class)->disableOriginalConstructor()->setMethods(['getCheckoutValues', 'getSuccessValues'])
        ->getMock();

        if ($returnUpdateError) {
            $klarnaCheckoutApiMock
                ->expects($this->any())
                ->method('getCheckoutValues')
                ->willThrowException(new NoShippingException());
        } else {
            $klarnaCheckoutApiMock
                ->expects($this->any())
                ->method('getCheckoutValues')
                ->willReturn(
                    [
                        'snippet' => 'testSnippet',
                        'orderid' => 'testOrderId',
                    ]
                );
        }
        if ($returnUpdateError) {
            $klarnaCheckoutApiMock
                ->expects($this->any())
                ->method('getSuccessValues')
                ->willReturn(
                    [
                        'snippet' => '',
                        'orderid' => '',
                    ]
                );
        } else {
            $klarnaCheckoutApiMock
                ->expects($this->any())
                ->method('getSuccessValues')
                ->willReturn(
                    [
                        'snippet' => 'testSuccessSnippet',
                        'orderid' => 'testSuccessOrderId',
                    ]
                );
        }

        return new KlarnaCheckout($klarnaCheckoutApiMock, $checkoutFacadeMock);
    }
}
