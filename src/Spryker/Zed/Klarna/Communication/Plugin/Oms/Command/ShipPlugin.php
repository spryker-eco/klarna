<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Zed\Klarna\Communication\Plugin\Oms\Command;

use Orm\Zed\Sales\Persistence\SpySalesOrder;
use Spryker\Zed\Oms\Business\Util\ReadOnlyArrayObject;
use Spryker\Zed\Oms\Communication\Plugin\Oms\Command\CommandByOrderInterface;

/**
 * Class CapturePlugin
 *
 * @package Spryker\Zed\Klarna\Communication\Plugin\Oms\Command
 *
 * @method \Spryker\Zed\Klarna\Business\KlarnaFacade getFacade()
 * @method \Spryker\Zed\Klarna\Communication\KlarnaCommunicationFactory getFactory()
 */
class ShipPlugin extends AbstractPlugin implements CommandByOrderInterface
{

    /**
     * @param array $orderItems
     * @param \Orm\Zed\Sales\Persistence\SpySalesOrder $orderEntity
     * @param \Spryker\Zed\Oms\Business\Util\ReadOnlyArrayObject $data
     *
     * @return array
     */
    public function run(array $orderItems, SpySalesOrder $orderEntity, ReadOnlyArrayObject $data)
    {
        $paymentEntity = $this->getPaymentEntity($orderEntity);

        $this->getFacade()->ship($paymentEntity);

        return [];
    }

}
