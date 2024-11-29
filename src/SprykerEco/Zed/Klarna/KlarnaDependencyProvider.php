<?php

/**
 * MIT License
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace SprykerEco\Zed\Klarna;

use Spryker\Yves\Kernel\Plugin\Pimple;
use Spryker\Zed\Kernel\AbstractBundleDependencyProvider;
use Spryker\Zed\Kernel\Container;
use SprykerEco\Zed\Klarna\Dependency\Facade\KlarnaToCheckoutBridge;
use SprykerEco\Zed\Klarna\Dependency\Facade\KlarnaToLocaleBridge;
use SprykerEco\Zed\Klarna\Dependency\Facade\KlarnaToMoneyBridge;
use SprykerEco\Zed\Klarna\Dependency\Facade\KlarnaToSalesBridge;

/**
 * Class KlarnaDependencyProvider
 *
 * @package SprykerEco\Zed\Klarna
 *
 * @author Daniel Bohnhardt <daniel.bohnhardt@twt.de>
 * @author Sergey Sikachev <sergey.sikachev@spryker.com>
 */
class KlarnaDependencyProvider extends AbstractBundleDependencyProvider
{

    const FACADE_SALES = 'sales facade';
    const FACADE_CHECKOUT = 'checkout facade';
    const FACADE_LOCALE = 'locale facade';
    const FACADE_MONEY = 'money facade';

    const PLUGIN_APPLICATION = 'applicaton plugin';

    /**
     * @param \Spryker\Zed\Kernel\Container $container
     *
     * @return \Spryker\Zed\Kernel\Container
     */
    public function provideCommunicationLayerDependencies(Container $container)
    {
        $container[static::FACADE_SALES] = function (Container $container) {
            return new KlarnaToSalesBridge($container->getLocator()->sales()->facade());
        };

        return $container;
    }

    /**
     * @param \Spryker\Zed\Kernel\Container $container
     *
     * @return \Spryker\Zed\Kernel\Container
     */
    public function provideBusinessLayerDependencies(Container $container)
    {
        $container[static::FACADE_CHECKOUT] = function (Container $container) {
            return new KlarnaToCheckoutBridge($container->getLocator()->checkout()->facade());
        };
        $container[static::FACADE_LOCALE] = function (Container $container) {
            return new KlarnaToLocaleBridge($container->getLocator()->locale()->facade());
        };
        $container[static::FACADE_MONEY] = function (Container $container) {
            return new KlarnaToMoneyBridge($container->getLocator()->money()->facade());
        };
        $container[static::PLUGIN_APPLICATION] = function () {
            $pimplePlugin = new Pimple();
            return $pimplePlugin->getApplication();
        };

        return $container;
    }

}
