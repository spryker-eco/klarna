<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace SprykerEco\Zed\Klarna;

use Spryker\Zed\Kernel\AbstractBundleDependencyProvider;
use Spryker\Zed\Kernel\Container;
use SprykerEco\Zed\Klarna\Dependency\Facade\KlarnaToCheckoutBridge;
use SprykerEco\Zed\Klarna\Dependency\Facade\KlarnaToLocaleBridge;
use SprykerEco\Zed\Klarna\Dependency\Facade\KlarnaToSalesBridge;

/**
 * Class KlarnaDependencyProvider
 *
 * @package SprykerEco\Zed\Klarna
 *
 * @author Daniel Bohnhardt <daniel.bohnhardt@twt.de>
 */
class KlarnaDependencyProvider extends AbstractBundleDependencyProvider
{

    const FACADE_SALES = 'sales facade';

    const FACADE_CHECKOUT = 'checkout_facade';
    const FACADE_LOCALE = 'locale_facade';

    /**
     * @param \Spryker\Zed\Kernel\Container $container
     *
     * @return \Spryker\Zed\Kernel\Container
     */
    public function provideCommunicationLayerDependencies(Container $container)
    {
        $container[self::FACADE_SALES] = function (Container $container) {
            return new KlarnaToSalesBridge($container->getLocator()->salesAggregator()->facade());
        };

        return $container;
    }

    /**
     * @param \Spryker\Zed\Kernel\Container $container
     *
     * @return \Spryker\Zed\Kernel\Container
     * @author Daniel Bohnhardt <daniel.bohnhardt@twt.de>
     */
    public function provideBusinessLayerDependencies(Container $container)
    {
        $container[self::FACADE_CHECKOUT] = function (Container $container) {
            return new KlarnaToCheckoutBridge($container->getLocator()->checkout()->facade());
        };
        $container[self::FACADE_LOCALE] = function (Container $container) {
            return new KlarnaToLocaleBridge($container->getLocator()->locale()->facade());
        };

        return $container;
    }

}
