<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Client\Klarna\Zed;

use Generated\Shared\Transfer\KlarnaCheckoutServiceRequestTransfer;
use Generated\Shared\Transfer\KlarnaCheckoutTransfer;
use Generated\Shared\Transfer\KlarnaGetAddressesRequestTransfer;
use Generated\Shared\Transfer\QuoteTransfer;
use Spryker\Client\ZedRequest\ZedRequestClient;

/**
 * Class KlarnaStub
 *
 * @package Spryker\Client\Klarna\Zed
 *
 * @author Daniel Bohnhardt <daniel.bohnhardt@twt.de>
 */
class KlarnaStub implements KlarnaStubInterface
{

    /**
     * @var \Spryker\Client\ZedRequest\ZedRequestClient
     */
    protected $zedRequestClient;

    /**
     * @param \Spryker\Client\ZedRequest\ZedRequestClient $zedRequestClient
     */
    public function __construct(ZedRequestClient $zedRequestClient)
    {
        $this->zedRequestClient = $zedRequestClient;
    }

    /**
     * @param \Generated\Shared\Transfer\QuoteTransfer $quoteTransfer
     *
     * @return \Spryker\Shared\Transfer\TransferInterface
     * @author Daniel Bohnhardt <daniel.bohnhardt@twt.de>
     */
    public function updatePayment(QuoteTransfer $quoteTransfer)
    {
        return $this->zedRequestClient->call('/klarna/gateway/update-payment', $quoteTransfer);
    }

    /**
     * @param \Generated\Shared\Transfer\QuoteTransfer $quoteTransfer
     *
     * @return \Generated\Shared\Transfer\KlarnaInstallmentResponseTransfer
     * @author Daniel Bohnhardt <daniel.bohnhardt@twt.de>
     */
    public function getInstallments(QuoteTransfer $quoteTransfer)
    {
        return $this->zedRequestClient->call('/klarna/gateway/installments', $quoteTransfer);
    }

    /**
     * @param \Generated\Shared\Transfer\QuoteTransfer $quoteTransfer
     *
     * @return \Spryker\Shared\Transfer\TransferInterface
     * @author Daniel Bohnhardt <daniel.bohnhardt@twt.de>
     */
    public function getKlarnaCheckoutHtml(QuoteTransfer $quoteTransfer)
    {
        return $this->zedRequestClient->call('/klarna/gateway/klarna-checkout', $quoteTransfer);
    }

    /**
     * @param \Generated\Shared\Transfer\KlarnaCheckoutTransfer $klarnaCheckoutTransfer
     *
     * @return \Generated\Shared\Transfer\KlarnaCheckoutTransfer
     * @author Daniel Bohnhardt <daniel.bohnhardt@twt.de>
     */
    public function getSuccessHtml(KlarnaCheckoutTransfer $klarnaCheckoutTransfer)
    {
        return $this->zedRequestClient->call('/klarna/gateway/klarna-success', $klarnaCheckoutTransfer);
    }

    /**
     * @param \Generated\Shared\Transfer\KlarnaCheckoutTransfer $klarnaCheckoutTransfer
     *
     * @return \Spryker\Shared\Transfer\TransferInterface
     * @author Daniel Bohnhardt <daniel.bohnhardt@twt.de>
     */
    public function createCheckoutOrder(KlarnaCheckoutTransfer $klarnaCheckoutTransfer)
    {
        return $this->zedRequestClient->call('/klarna/gateway/create-checkout-order', $klarnaCheckoutTransfer);
    }

    /**
     * @param \Generated\Shared\Transfer\KlarnaCheckoutServiceRequestTransfer $klarnaCheckoutServiceRequestTransfer
     *
     * @return \Generated\Shared\Transfer\KlarnaCheckoutServiceResponseTransfer
     */
    public function checkoutService(KlarnaCheckoutServiceRequestTransfer $klarnaCheckoutServiceRequestTransfer)
    {
        return $this->zedRequestClient->call('/klarna/gateway/checkout-service', $klarnaCheckoutServiceRequestTransfer);
    }

    /**
     * @param \Generated\Shared\Transfer\KlarnaGetAddressesRequestTransfer $klarnaGetAddressesRequestTransfer
     *
     * @return \Generated\Shared\Transfer\KlarnaGetAddressesResponseTransfer
     */
    public function getAddresses(KlarnaGetAddressesRequestTransfer $klarnaGetAddressesRequestTransfer)
    {
        return $this->zedRequestClient->call('/klarna/gateway/get-addresses', $klarnaGetAddressesRequestTransfer);
    }

}
