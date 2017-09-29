<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Zed\Klarna\Communication\Plugin\Twig\Functions;

use Spryker\Client\Cart\CartClientInterface;
use Spryker\Client\Klarna\KlarnaClientInterface;

/**
 * Class KlarnaCheckout
 *
 * @author Daniel Bohnhardt <daniel.bohnhardt@twt.de>
 */
class KlarnaCheckout
{

    /**
     * @var \Spryker\Client\Klarna\KlarnaClientInterface
     *
     * @author Daniel Bohnhardt <daniel.bohnhardt@twt.de>
     */
    protected $klarnaClient;

    /**
     * @var \Spryker\Client\Cart\CartClientInterface
     *
     * @author Daniel Bohnhardt <daniel.bohnhardt@twt.de>
     */
    protected $cartClient;

    /**
     * KlarnaCheckout constructor.
     *
     * @param \Spryker\Client\Klarna\KlarnaClientInterface $klarnaClient
     *
     * @author Daniel Bohnhardt <daniel.bohnhardt@twt.de>
     */
    public function __construct(KlarnaClientInterface $klarnaClient, CartClientInterface $cartClient)
    {
        $this->klarnaClient = $klarnaClient;
        $this->cartClient = $cartClient;
    }

    /**
     * @return string
     * @author Daniel Bohnhardt <daniel.bohnhardt@twt.de>
     */
    public function renderKlarnaHtml()
    {
        $quoteTransfer = $this->cartClient->getQuote();

        /** @var \Generated\Shared\Transfer\KlarnaCheckoutTransfer $response */
        $response = $this->klarnaClient->getKlarnaCheckoutHtml($quoteTransfer);

        if ($response->getOrderid()) {
            $this->klarnaClient->storeKlarnaOrderIdInSession($response->getOrderid());
        }

        return $response->getHtml();
    }

}
