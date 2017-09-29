<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Zed\Klarna\Communication\Plugin\Oms\Condition;

use Orm\Zed\Sales\Persistence\SpySalesOrderItem;
use Spryker\Shared\Klarna\KlarnaConstants;
use Spryker\Zed\Oms\Communication\Plugin\Oms\Condition\ConditionInterface;

/**
 * Class IsOrderDenied
 *
 * @package Spryker\Zed\Klarna\Communication\Plugin\Oms\Condition
 * @method \Spryker\Zed\Klarna\Business\KlarnaFacade getFacade()
 * @method \Spryker\Zed\Klarna\Communication\KlarnaCommunicationFactory getFactory()
 *
 * @author Daniel Bohnhardt <daniel.bohnhardt@twt.de>
 */
class IsOrderDenied extends AbstractPlugin implements ConditionInterface
{

    /**
     * @param \Orm\Zed\Sales\Persistence\SpySalesOrderItem $orderItem
     *
     * @return bool
     */
    public function check(SpySalesOrderItem $orderItem)
    {
        $paymentEntity = $this->getPaymentEntity($orderItem);

        return (int)$paymentEntity->getStatus() === KlarnaConstants::ORDER_PENDING_DENIED;
    }

}
