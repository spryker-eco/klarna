<?php

/**
 * MIT License
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace SprykerEco\Yves\Klarna;

use Spryker\Yves\Kernel\AbstractBundleDependencyProvider;
use Spryker\Yves\Kernel\Container;
use Spryker\Yves\Kernel\Plugin\Pimple;

/**
 * Class KlarnaDependencyProvider
 *
 * @package SprykerEco\Yves\Klarna
 */
class KlarnaDependencyProvider extends AbstractBundleDependencyProvider
{

    const CLIENT_KLARNA = 'klarna client';
    const CLIENT_CART = 'cart client';
    const CLIENT_GLOSSARY = 'glossary client';
    const PLUGIN_APPLICATION = 'plugin application';

    /**
     * @param \Spryker\Yves\Kernel\Container $container
     *
     * @return \Spryker\Yves\Kernel\Container
     */
    public function provideDependencies(Container $container)
    {
        $container = $this->provideClients($container);
        $container = $this->providePlugins($container);

        return $container;
    }

    /**
     * @param \Spryker\Yves\Kernel\Container $container
     *
     * @return \Spryker\Yves\Kernel\Container
     */
    protected function provideClients(Container $container)
    {
        $container[static::CLIENT_KLARNA] = function (Container $container) {
            return $container->getLocator()->klarna()->client();
        };
        $container[static::CLIENT_CART] = function (Container $container) {
            return $container->getLocator()->cart()->client();
        };
        $container[static::CLIENT_GLOSSARY] = function (Container $container) {
            return $container->getLocator()->glossary()->client();
        };

        return $container;
    }

    /**
     * @param \Spryker\Yves\Kernel\Container $container
     *
     * @return \Spryker\Yves\Kernel\Container
     */
    protected function providePlugins(Container $container)
    {
        $container[self::PLUGIN_APPLICATION] = function () {
            $pimplePlugin = new Pimple();

            return $pimplePlugin->getApplication();
        };

        return $container;
    }

}
