<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Zed\Klarna\Communication\Plugin\Oms\Command;

use Orm\Zed\Sales\Persistence\SpySalesOrder;
use Spryker\Zed\Kernel\Communication\AbstractPlugin as BaseAbstractPlugin;

/**
 * Class AbstractPlugin
 *
 * @package Spryker\Zed\Klarna\Communication\Plugin\Oms\Command
 *
 * @author Daniel Bohnhardt <daniel.bohnhardt@twt.de>
 */
class AbstractPlugin extends BaseAbstractPlugin
{

    /**
     * @param \Orm\Zed\Sales\Persistence\SpySalesOrder $orderEntity
     *
     * @return \Orm\Zed\Klarna\Persistence\SpyPaymentKlarna
     */
    protected function getPaymentEntity(SpySalesOrder $orderEntity)
    {
        $payment = $orderEntity->getSpyPaymentKlarnas()->getFirst();

        return $payment;
    }

}
