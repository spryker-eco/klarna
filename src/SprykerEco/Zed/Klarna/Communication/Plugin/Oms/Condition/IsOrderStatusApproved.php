<?php

/**
 * MIT License
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace SprykerEco\Zed\Klarna\Communication\Plugin\Oms\Condition;

use Orm\Zed\Sales\Persistence\SpySalesOrderItem;
use Spryker\Zed\Oms\Communication\Plugin\Oms\Condition\ConditionInterface;
use SprykerEco\Shared\Klarna\KlarnaConstants;

/**
 * Class IsOrderStatusApproved
 *
 * @package SprykerEco\Zed\Klarna\Communication\Plugin\Oms\Condition
 * @method \SprykerEco\Zed\Klarna\Business\KlarnaFacade getFacade()
 * @method \SprykerEco\Zed\Klarna\Communication\KlarnaCommunicationFactory getFactory()
 * @author Daniel Bohnhardt <daniel.bohnhardt@twt.de>
 */
class IsOrderStatusApproved extends AbstractPlugin implements ConditionInterface
{

    /**
     * @param \Orm\Zed\Sales\Persistence\SpySalesOrderItem $orderItem
     *
     * @return bool
     */
    public function check(SpySalesOrderItem $orderItem)
    {
        $paymentEntity = $this->getPaymentEntity($orderItem);

        return ((int)$paymentEntity->getStatus() === KlarnaConstants::ORDER_PENDING_ACCEPTED);
    }

}
