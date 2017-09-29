<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Zed\Klarna\Communication\Plugin\Oms\Condition;

use Orm\Zed\Sales\Persistence\SpySalesOrderItem;
use Spryker\Zed\Oms\Communication\Plugin\Oms\Condition\ConditionInterface;

/**
 * Class HasCapturePlugin
 *
 * @package Spryker\Zed\Klarna\Communication\Plugin\Oms\Condition
 * @method \Spryker\Zed\Klarna\Business\KlarnaFacade getFacade()
 * @method \Spryker\Zed\Klarna\Communication\KlarnaCommunicationFactory getFactory()
 * @author Daniel Bohnhardt <daniel.bohnhardt@twt.de>
 */
class HasCapturePlugin extends AbstractPlugin implements ConditionInterface
{

    /**
     * @param \Orm\Zed\Sales\Persistence\SpySalesOrderItem $orderItem
     *
     * @return bool
     */
    public function check(SpySalesOrderItem $orderItem)
    {
        $return = (bool)$this->getPaymentEntity($orderItem)->getInvoiceId();

        if (!$return) {
            $orderItems = $orderItem->getSpyPaymentKlarnaOrderItems();
            foreach ($orderItems as $klarnaOrderItem) {
                if ($klarnaOrderItem->getInvoiceId()) {
                    $return = true;
                    break;
                }
            }
        }

        return $return;
    }

}
