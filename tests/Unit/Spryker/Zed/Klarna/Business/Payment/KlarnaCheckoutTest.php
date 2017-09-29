<?php
/*
 * This file is part of the TWT eCommerce platform package.
 *
 * (c) TWT Interactive GmbH <info@twt.de>
 *
 * For the full copyright, license and further information contact TWT.
*/

use Codeception\TestCase\Test;
use Generated\Shared\Transfer\KlarnaCheckoutTransfer;
use Generated\Shared\Transfer\QuoteTransfer;
use Spryker\Shared\Klarna\KlarnaConstants;
use Spryker\Zed\Klarna\Business\Exception\NoShippingException;
use Spryker\Zed\Klarna\Business\Request\KlarnaCheckout;

/**
 * Class KlarnaCheckoutTest
 *
 * @author   Daniel Bohnhardt <daniel.bohnhardt@twt.de>
 */
class KlarnaCheckoutTest extends Test
{

    /**
     * @return void
     * @author Daniel Bohnhardt <daniel.bohnhardt@twt.de>
     */
    public function testGetCheckoutHtml()
    {
        $quoteTransfer = new QuoteTransfer();

        $klarnaCheckout = $this->getKlarnaCheckoutObject();
        $result         = $klarnaCheckout->getCheckoutHtml($quoteTransfer);

        $this->assertInstanceOf('\Generated\Shared\Transfer\KlarnaCheckoutTransfer', $result);
        $this->assertSame('testSnippet', $result->getHtml());
        $this->assertSame('testOrderId', $result->getOrderid());
    }

    /**
     * @return void
     * @author Daniel Bohnhardt <daniel.bohnhardt@twt.de>
     */
    public function testGetCheckoutHtmlFail()
    {
        $quoteTransfer = new QuoteTransfer();

        $klarnaCheckout = $this->getKlarnaCheckoutObject(true);
        $result         = $klarnaCheckout->getCheckoutHtml($quoteTransfer);

        $this->assertInstanceOf('\Generated\Shared\Transfer\KlarnaCheckoutTransfer', $result);
        $this->assertSame('', $result->getHtml());
        $this->assertSame('', $result->getOrderid());
    }

    /**
     * @return void
     * @author Daniel Bohnhardt <daniel.bohnhardt@twt.de>
     */
    public function testGetSuccessHtml()
    {
        $klarnaCheckoutTransfer = new KlarnaCheckoutTransfer();
        $klarnaCheckoutTransfer->setOrderid('12345');

        $klarnaCheckout = $this->getKlarnaCheckoutObject();
        $result         = $klarnaCheckout->getSuccessHtml($klarnaCheckoutTransfer);

        $this->assertInstanceOf('\Generated\Shared\Transfer\KlarnaCheckoutTransfer', $result);
        $this->assertSame('testSuccessSnippet', $result->getHtml());
        $this->assertSame('testSuccessOrderId', $result->getOrderid());
    }

    /**
     * @return void
     * @author Daniel Bohnhardt <daniel.bohnhardt@twt.de>
     */
    public function testGetSuccessHtmlFail()
    {
        $klarnaCheckoutTransfer = new KlarnaCheckoutTransfer();
        $klarnaCheckoutTransfer->setOrderid('12345');

        $klarnaCheckout = $this->getKlarnaCheckoutObject(true);
        $result         = $klarnaCheckout->getSuccessHtml($klarnaCheckoutTransfer);

        $this->assertInstanceOf('\Generated\Shared\Transfer\KlarnaCheckoutTransfer', $result);
        $this->assertSame('', $result->getHtml());
        $this->assertSame('', $result->getOrderid());
    }

    /**
     * @return void
     * @author Daniel Bohnhardt <daniel.bohnhardt@twt.de>
     */
    public function testCreateCheckoutOrder()
    {
        $klarnaCheckoutTransfer = new KlarnaCheckoutTransfer();
        $klarnaCheckoutTransfer->setOrderid('12345');

        $klarnaCheckout = $this->getKlarnaCheckoutCreateOrderObject(true);
        $result         = $klarnaCheckout->createCheckoutOrder($klarnaCheckoutTransfer);

        $this->assertTrue($result);
    }

    /**
     * @param bool $returnUpdateError
     *
     * @return \Spryker\Zed\Klarna\Business\Request\KlarnaCheckout
     * @author Daniel Bohnhardt <daniel.bohnhardt@twt.de>
     */
    protected function getKlarnaCheckoutCreateOrderObject($returnUpdateError = false)
    {
        $checkoutFacadeMock = $this->getMock(
            'Spryker\Zed\Klarna\Dependency\Facade\KlarnaToCheckoutBridgeInterface',
            [
                'placeOrder',
            ]
        );
        $checkoutResponseTransfer = new \Generated\Shared\Transfer\CheckoutResponseTransfer();
        $checkoutFacadeMock->expects($this->any())->method('placeOrder')->willReturn($checkoutResponseTransfer);

        $klarnaCheckoutApiMock = $this->getMock(
            'Spryker\Zed\Klarna\Business\Api\Handler\KlarnaCheckoutApi',
            [
                'createOrder', 'fetchKlarnaOrder'
            ],
            [],
            '',
            false
        );

        $fetchKlarnaOrderReturnData = [
            'status'            => KlarnaConstants::STATUS_COMPLETE,
            'cart'              => [
                'items' => [
                    [
                        'type'          => KlarnaConstants::SHIPPING_TYPE,
                        'reference'     => '',
                        'name'          => 'shipping',
                        'quantity'      => 1,
                        'unit_price'    => 4.95,
                        'discount_rate' => 0,
                        'tax_rate'      => 19
                    ],
                    [
                        'type'          => '',
                        'reference'     => '',
                        'name'          => 'Artikel',
                        'quantity'      => 1,
                        'unit_price'    => 9.95,
                        'discount_rate' => 0,
                        'tax_rate'      => 19
                    ],
                ],
                'total_price_including_tax' => 14.90,
            ],
            'billing_address'   => [
                'given_name'    => 'testperson',
                'family_name'   => 'lastname',
                'email'         => 'test@test.de',
                'city'          => 'testcity',
                'street_name'   => 'teststreet',
                'street_number' => 'teststreetnumber',
                'title'         => 'Herr',
                'country'       => 'DE',
                'postal_code'   => '41460'

            ],
            'shipping_address'  => [
                'given_name'    => 'testperson',
                'family_name'   => 'lastname',
                'email'         => 'test@test.de',
                'city'          => 'testcity',
                'street_name'   => 'teststreet',
                'street_number' => 'teststreetnumber',
                'title'         => 'Herr',
                'country'       => 'DE',
                'postal_code'   => '41460'
            ],
            'customer'          => [
                'date_of_birth' => '',
                'gender'        => 'Herr',
            ],
            'purchase_country'  => 'DE',
            'purchase_currency' => 'EUR',
            'reservation'       => 'reservationId',

        ];

        $connector              = $this->getMock(
            '\Klarna_Checkout_ConnectorInterface'
        );
        $fetchKlarnaOrderReturn = new \Klarna_Checkout_Order($connector);
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
     * @return \Spryker\Zed\Klarna\Business\Request\KlarnaCheckout
     * @author Daniel Bohnhardt <daniel.bohnhardt@twt.de>
     */
    protected function getKlarnaCheckoutObject($returnUpdateError = false)
    {
        $checkoutFacadeMock = $this->getMock(
            'Spryker\Zed\Klarna\Dependency\Facade\KlarnaToCheckoutBridgeInterface',
            [
                'placeOrder'
            ]
        );

        $klarnaCheckoutApiMock = $this->getMock(
            'Spryker\Zed\Klarna\Business\Api\Handler\KlarnaCheckoutApi',
            [
                'getCheckoutValues', 'getSuccessValues'
            ],
            [],
            '',
            false
        );

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
