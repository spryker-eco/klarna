<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace SprykerEco\Zed\Klarna\Communication\Plugin\Oms\Command;

use ArrayObject;
use Generated\Shared\Transfer\AddressTransfer;
use Generated\Shared\Transfer\ItemTransfer;
use Generated\Shared\Transfer\KlarnaPaymentTransfer;
use Generated\Shared\Transfer\PaymentTransfer;
use Generated\Shared\Transfer\QuoteTransfer;
use Generated\Shared\Transfer\ShipmentMethodTransfer;
use Generated\Shared\Transfer\ShipmentTransfer;
use Orm\Zed\Sales\Persistence\SpySalesOrder;
use Spryker\Zed\Oms\Business\Util\ReadOnlyArrayObject;
use Spryker\Zed\Oms\Communication\Plugin\Oms\Command\CommandByOrderInterface;
use SprykerEco\Zed\Klarna\Communication\Plugin\Exception\KlarnaUpdateException;

/**
 * Class UpdatePlugin
 *
 * @package SprykerEco\Zed\Klarna\Communication\Plugin\Oms\Command
 *
 * @method \SprykerEco\Zed\Klarna\Business\KlarnaFacade getFacade()
 * @method \SprykerEco\Zed\Klarna\Communication\KlarnaCommunicationFactory getFactory()
 * @author Daniel Bohnhardt <daniel.bohnhardt@twt.de>
 */
class UpdatePlugin extends AbstractPlugin implements CommandByOrderInterface
{

    /**
     * @param \Orm\Zed\Sales\Persistence\SpySalesOrderItem[] $orderItems
     * @param \Orm\Zed\Sales\Persistence\SpySalesOrder $orderEntity
     * @param \Spryker\Zed\Oms\Business\Util\ReadOnlyArrayObject $data
     *
     * @throws \SprykerEco\Zed\Klarna\Communication\Plugin\Exception\KlarnaUpdateException
     *
     * @return array
     */
    public function run(array $orderItems, SpySalesOrder $orderEntity, ReadOnlyArrayObject $data)
    {
        $quoteTransfer = new QuoteTransfer();
        $quoteTransfer->fromArray($orderEntity->toArray(), true);
        $paymentEntity = $this->getPaymentEntity($orderEntity);

        $klarnaTransfer = new KlarnaPaymentTransfer();

        $klarnaTransfer->fromArray($paymentEntity->toArray(), true);

        $paymentTransfer = new PaymentTransfer();
        $paymentTransfer->setKlarna($klarnaTransfer);

        $billingAddress = $orderEntity->getBillingAddress();
        $billingAddressTransfer = new AddressTransfer();
        $billingAddressTransfer->fromArray($billingAddress->toArray(), true);
        $billingAddressTransfer->setIso2Code($billingAddress->getCountry()->getIso2Code());

        $shipmentMethodEntity = $orderEntity->getShipmentMethod();
        $shipmentMethodTransfer = new ShipmentMethodTransfer();
        $shipmentMethodTransfer->fromArray($shipmentMethodEntity->toArray(), true);

        $taxSet = $shipmentMethodEntity->getTaxSet();
        if ($taxSet) {
            $taxRates = $taxSet->getSpyTaxRates();
            $shipmentMethodTransfer->setTaxRate($taxRates->get(0)->getRate());
        }
        $shipmentTransfer = new ShipmentTransfer();
        $shipmentTransfer->setMethod($shipmentMethodTransfer);

        $items = new ArrayObject();

        foreach ($orderEntity->getItems() as $orderItem) {
            $itemTransfer = new ItemTransfer();
            $itemTransfer->fromArray($orderItem->toArray(), true);
            $itemTransfer->setUnitGrossPrice($orderItem->getGrossPrice());
            $items->append($itemTransfer);
        }

        $quoteTransfer->setPayment($paymentTransfer);
        $quoteTransfer->setBillingAddress($billingAddressTransfer);
        $quoteTransfer->setItems($items);
        $quoteTransfer->setShipment($shipmentTransfer);

        $result = $this->getFacade()->updatePayment($quoteTransfer);

        if ($result->getStatus() === 0) {
            throw new KlarnaUpdateException($result->getError());
        }

        return [];
    }

}
