<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace SprykerEco\Zed\Klarna\Communication\Plugin\Oms\Command;

use Orm\Zed\Sales\Persistence\SpySalesOrder;
use SprykerEco\Shared\Klarna\KlarnaConstants;
use Spryker\Zed\Oms\Business\Util\ReadOnlyArrayObject;
use Spryker\Zed\Oms\Communication\Plugin\Oms\Command\CommandByOrderInterface;

/**
 * Class CapturePlugin
 *
 * @package SprykerEco\Zed\Klarna\Communication\Plugin\Oms\Command
 *
 * @method \SprykerEco\Zed\Klarna\Business\KlarnaFacade getFacade()
 * @method \SprykerEco\Zed\Klarna\Communication\KlarnaCommunicationFactory getFactory()
 * @author Daniel Bohnhardt <daniel.bohnhardt@twt.de>
 */
class CapturePlugin extends AbstractPlugin implements CommandByOrderInterface
{

    /**
     * @param array $orderItems
     * @param \Orm\Zed\Sales\Persistence\SpySalesOrder $orderEntity
     * @param \Spryker\Zed\Oms\Business\Util\ReadOnlyArrayObject $data
     *
     * @return array
     * @author Daniel Bohnhardt <daniel.bohnhardt@twt.de>
     */
    public function run(array $orderItems, SpySalesOrder $orderEntity, ReadOnlyArrayObject $data)
    {
        $orderTransfer = $this->getOrderTransfer($orderEntity);
        $paymentEntity = $this->getPaymentEntity($orderEntity);

        $result = $this->getFacade()->capturePartPayment($orderItems, $paymentEntity, $orderTransfer);
        if ($result[0] === KlarnaConstants::KLARNA_ACTIVATE_SUCCESS) {
            $articleIds = [];
            foreach ($orderItems as $orderItem) {
                $articleIds[] = $orderItem->getIdSalesOrderItem();
            }

            // activate shipment with first item
            if (!$paymentEntity->getShippingInvoiceId()) {
                $paymentEntity->setShippingInvoiceId($result[1]);
                $paymentEntity->save();
            }

            if (count($articleIds)) {
                $paymentOrderItems = $paymentEntity->getSpyPaymentKlarnaOrderItems();
                foreach ($paymentOrderItems as $paymentOrderItem) {
                    if (in_array($paymentOrderItem->getFkSalesOrderItem(), $articleIds)) {
                        $paymentOrderItem->setInvoiceId($result[1]);
                        $paymentOrderItem->save();
                    }
                }
            }
        }

        return [];
    }

    /**
     * @param \Orm\Zed\Sales\Persistence\SpySalesOrder $orderEntity
     *
     * @return \Generated\Shared\Transfer\OrderTransfer
     */
    protected function getOrderTransfer(SpySalesOrder $orderEntity)
    {
        return $this
            ->getFactory()
            ->getSalesFacade()
            ->getOrderTotalsByIdSalesOrder($orderEntity->getIdSalesOrder());
    }

}
