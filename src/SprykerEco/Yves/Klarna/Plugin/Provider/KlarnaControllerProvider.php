<?php

/**
 * MIT License
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace SprykerEco\Yves\Klarna\Plugin\Provider;

use Pyz\Yves\Application\Plugin\Provider\AbstractYvesControllerProvider;
use Silex\Application;

/**
 * Class KlarnaControllerProvider
 *
 * @package SprykerEco\Yves\Klarna\Plugin\Provider
 *
 * @author Daniel Bohnhardt <daniel.bohnhardt@twt.de>
 */
class KlarnaControllerProvider extends AbstractYvesControllerProvider
{
    const CHECKOUT_KLARNA_SUCCESS = 'checkout-klarna-success';
    const CHECKOUT_KLARNA_PUSH = 'checkout-klarna-push';
    const KLARNA_GET_ADDRESSES = 'klarna-get-addresses';
    const KLARNA_PAYMENT_METHOD = 'klarna-payment-method';
    const KLARNA_INSTALLMENTS = 'klarna-installments';

    /**
     * @param \Silex\Application $app
     *
     * @return void
     */
    protected function defineControllers(Application $app)
    {
        $this->createController('/checkout/klarna/success', self::CHECKOUT_KLARNA_SUCCESS, 'Klarna', 'Klarna', 'success')
             ->method('GET|POST');
        $this->createController('/checkout/klarna/push', self::CHECKOUT_KLARNA_PUSH, 'Klarna', 'Klarna', 'push')
             ->method('GET|POST');
        $this->createController('/klarna/get-addresses', self::KLARNA_GET_ADDRESSES, 'Klarna', 'Klarna', 'getAddresses')
            ->method('GET|POST');
        $this->createController('/klarna/checkout-service', self::KLARNA_PAYMENT_METHOD, 'Klarna', 'Klarna', 'checkoutService')
            ->method('GET|POST');
        $this->createController('/klarna/installments', self::KLARNA_INSTALLMENTS, 'Klarna', 'Klarna', 'installments')
            ->method('GET|POST');
    }
}
