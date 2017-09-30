<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace SprykerEco\Zed\Klarna\Communication\Plugin\Twig;

use Spryker\Client\Cart\CartClientInterface;
use SprykerEco\Client\Klarna\KlarnaClientInterface;
use SprykerEco\Zed\Klarna\Communication\Plugin\Twig\Functions\KlarnaCheckout;

/**
 * Class KlarnaCheckoutTwigExtension
 *
 * @package SprykerEco\Zed\Klarna\Communication\Plugin\Twig
 *
 * @author Daniel Bohnhardt <daniel.bohnhardt@twt.de>
 */
class KlarnaCheckoutTwigExtension extends \Twig_Extension
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
     * KlarnaCheckoutTwigExtension constructor.
     *
     * @param \SprykerEco\Client\Klarna\KlarnaClientInterface $klarnaClient
     * @param \Spryker\Client\Cart\CartClientInterface $cartClient
     *
     * @author Daniel Bohnhardt <daniel.bohnhardt@twt.de>
     */
    public function __construct(KlarnaClientInterface $klarnaClient, CartClientInterface $cartClient)
    {
        $this->klarnaClient = $klarnaClient;
        $this->cartClient = $cartClient;
    }

    /**
     * @return array
     * @author Daniel Bohnhardt <daniel.bohnhardt@twt.de>
     */
    public function getFunctions()
    {
        return [
            new \Twig_SimpleFunction(
                'KlarnaCheckout',
                function () {
                    $klarnaCheckout = new KlarnaCheckout($this->klarnaClient, $this->cartClient);
                    return $klarnaCheckout->renderKlarnaHtml();
                }
            ),
        ];
    }

    /**
     * @return string
     * @author Daniel Bohnhardt <daniel.bohnhardt@twt.de>
     */
    public function getName()
    {
        return 'KlarnaCheckoutTwigExtension';
    }

}
