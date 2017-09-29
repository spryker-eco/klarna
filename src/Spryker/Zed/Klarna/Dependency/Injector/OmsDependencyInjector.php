<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Zed\Klarna\Dependency\Injector;

use Spryker\Zed\Kernel\Container;
use Spryker\Zed\Kernel\Dependency\Injector\AbstractDependencyInjector;
use Spryker\Zed\Klarna\Communication\Plugin\Oms\Command\CancelPlugin;
use Spryker\Zed\Klarna\Communication\Plugin\Oms\Command\CapturePlugin;
use Spryker\Zed\Klarna\Communication\Plugin\Oms\Command\CheckPlugin;
use Spryker\Zed\Klarna\Communication\Plugin\Oms\Command\RefundPlugin;
use Spryker\Zed\Klarna\Communication\Plugin\Oms\Command\ShipPlugin;
use Spryker\Zed\Klarna\Communication\Plugin\Oms\Command\UpdatePlugin;
use Spryker\Zed\Klarna\Communication\Plugin\Oms\Condition\HasCapturePlugin;
use Spryker\Zed\Klarna\Communication\Plugin\Oms\Condition\IsBillingAddressSamePlugin;
use Spryker\Zed\Klarna\Communication\Plugin\Oms\Condition\IsOrderDenied;
use Spryker\Zed\Klarna\Communication\Plugin\Oms\Condition\IsOrderStatusApproved;
use Spryker\Zed\Oms\Communication\Plugin\Oms\Command\CommandCollectionInterface;
use Spryker\Zed\Oms\Communication\Plugin\Oms\Condition\ConditionCollectionInterface;
use Spryker\Zed\Oms\OmsDependencyProvider;

class OmsDependencyInjector extends AbstractDependencyInjector
{

    /**
     * @param \Spryker\Zed\Kernel\Container $container
     *
     * @return \Spryker\Zed\Kernel\Container
     */
    public function injectBusinessLayerDependencies(Container $container)
    {
        $container = $this->injectCommands($container);
        $container = $this->injectConditions($container);

        return $container;
    }

    /**
     * @param \Spryker\Zed\Kernel\Container $container
     *
     * @return \Spryker\Zed\Kernel\Container
     */
    protected function injectCommands(Container $container)
    {
        $container->extend(OmsDependencyProvider::COMMAND_PLUGINS, function (CommandCollectionInterface $commandCollection) {
            $commandCollection
                ->add(new CancelPlugin(), 'Klarna/Cancel')
                ->add(new CapturePlugin(), 'Klarna/Capture')
                ->add(new CheckPlugin(), 'Klarna/Check')
                ->add(new UpdatePlugin(), 'Klarna/Update')
                ->add(new ShipPlugin(), 'Klarna/Ship')
                ->add(new RefundPlugin(), 'Klarna/Refund');

            return $commandCollection;
        });

        return $container;
    }

    /**
     * @param \Spryker\Zed\Kernel\Container $container
     *
     * @return \Spryker\Zed\Kernel\Container
     */
    protected function injectConditions(Container $container)
    {
        $container->extend(OmsDependencyProvider::CONDITION_PLUGINS, function (ConditionCollectionInterface $conditionCollection) {
            $conditionCollection
                ->add(new HasCapturePlugin(), 'Klarna/HasCapture')
                ->add(new IsOrderStatusApproved(), 'Klarna/IsOrderStatusApproved')
                ->add(new IsOrderDenied(), 'Klarna/IsOrderDenied')
                ->add(new IsBillingAddressSamePlugin(), 'Klarna/IsBillingAddressSame');

            return $conditionCollection;
        });

        return $container;
    }

}
