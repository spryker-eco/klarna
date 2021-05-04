<?php

/**
 * MIT License
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
     */
    protected $cartClient;

    /**
     * @param \SprykerEco\Client\Klarna\KlarnaClientInterface $klarnaClient
     * @param \Spryker\Client\Cart\CartClientInterface $cartClient
     */
    public function __construct(KlarnaClientInterface $klarnaClient, CartClientInterface $cartClient)
    {
        $this->klarnaClient = $klarnaClient;
        $this->cartClient = $cartClient;
    }

    /**
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
