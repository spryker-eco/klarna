<?php

/**
 * MIT License
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace SprykerEco\Zed\Klarna\Communication\Plugin\Oms\Command;

use Orm\Zed\Sales\Persistence\SpySalesOrder;
use Spryker\Zed\Oms\Business\Util\ReadOnlyArrayObject;
use Spryker\Zed\Oms\Communication\Plugin\Oms\Command\CommandByOrderInterface;

/**
 * Class CapturePlugin
 *
 * @package SprykerEco\Zed\Klarna\Communication\Plugin\Oms\Command
 *
 * @method \SprykerEco\Zed\Klarna\Business\KlarnaFacade getFacade()
 * @method \SprykerEco\Zed\Klarna\Communication\KlarnaCommunicationFactory getFactory()
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
