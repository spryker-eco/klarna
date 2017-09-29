<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Zed\Klarna\Communication\Plugin\Oms\Condition;

use Orm\Zed\Sales\Persistence\SpySalesOrderItem;
use Spryker\Zed\Oms\Communication\Plugin\Oms\Condition\ConditionInterface;

/**
 * Class IsBillingAddressSamePlugin
 *
 * @package Spryker\Zed\Klarna\Communication\Plugin\Oms\Condition
 * @method \Spryker\Zed\Klarna\Business\KlarnaFacade getFacade()
 * @method \Spryker\Zed\Klarna\Communication\KlarnaCommunicationFactory getFactory()
 *
 * @author Daniel Bohnhardt <daniel.bohnhardt@twt.de>
 */
class IsBillingAddressSamePlugin extends AbstractPlugin implements ConditionInterface
{

    /**
     * @param \Orm\Zed\Sales\Persistence\SpySalesOrderItem $orderItem
     *
     * @return bool
     */
    public function check(SpySalesOrderItem $orderItem)
    {
        $paymentEntity = $this->getPaymentEntity($orderItem);

        $order = $orderItem->getOrder();
        $billingAddress = $order->getBillingAddress();

        $street = $billingAddress->getAddress1() . ' ' . $billingAddress->getAddress2();
        $street .= strlen($billingAddress->getAddress3()) ? ' ' . $billingAddress->getAddress3() : '';
        $sPaymentHash = md5(
            $billingAddress->getFirstName() .
            $billingAddress->getLastName() .
            $street .
            $billingAddress->getZipCode() .
            $billingAddress->getCity() .
            $billingAddress->getCompany()
        );

        $sOrderHash = md5(
            $paymentEntity->getFirstName() .
            $paymentEntity->getLastName() .
            $paymentEntity->getStreet() .
            $paymentEntity->getZipCode() .
            $paymentEntity->getCity()
        );

        return $sPaymentHash === $sOrderHash;
    }

}
