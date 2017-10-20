<?php

/**
 * MIT License
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace SprykerEco\Zed\Klarna\Communication\Plugin\Oms\Condition;

use Orm\Zed\Sales\Persistence\SpySalesOrderItem;
use Spryker\Zed\Kernel\Communication\AbstractPlugin as BaseAbstractPlugin;

/**
 * Class AbstractPlugin
 *
 * @package SprykerEco\Zed\Klarna\Communication\Plugin\Oms\Condition
 *
 * @author Daniel Bohnhardt <daniel.bohnhardt@twt.de>
 */
class AbstractPlugin extends BaseAbstractPlugin
{
    /**
     * @author Daniel Bohnhardt <daniel.bohnhardt@twt.de>
     *
     * @param \Orm\Zed\Sales\Persistence\SpySalesOrderItem $orderItem
     *
     * @return \Orm\Zed\Klarna\Persistence\SpyPaymentKlarna
     */
    public function getPaymentEntity(SpySalesOrderItem $orderItem)
    {
        $order = $orderItem->getOrder();

        $payment = $order->getSpyPaymentKlarnas()->getFirst();

        return $payment;
    }
}
