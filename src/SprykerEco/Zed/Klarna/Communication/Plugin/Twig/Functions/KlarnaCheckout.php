<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace SprykerEco\Zed\Klarna\Communication\Plugin\Twig\Functions;

use Spryker\Client\Cart\CartClientInterface;
use SprykerEco\Client\Klarna\KlarnaClientInterface;

/**
 * Class KlarnaCheckout
 *
 * @author Daniel Bohnhardt <daniel.bohnhardt@twt.de>
 */
class KlarnaCheckout
{

    /**
     * @var \SprykerEco\Client\Klarna\KlarnaClientInterface
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
     * @author Daniel Bohnhardt <daniel.bohnhardt@twt.de>
     *
     * @param \SprykerEco\Client\Klarna\KlarnaClientInterface $klarnaClient
     */
    public function __construct(KlarnaClientInterface $klarnaClient, CartClientInterface $cartClient)
    {
        $this->klarnaClient = $klarnaClient;
        $this->cartClient = $cartClient;
    }

    /**
     * @author Daniel Bohnhardt <daniel.bohnhardt@twt.de>
     *
     * @return string
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
