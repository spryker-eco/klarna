<?php

/**
 * MIT License
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace SprykerEco\Zed\Klarna\Business\Order;

use Generated\Shared\Transfer\CheckoutResponseTransfer;
use Generated\Shared\Transfer\KlarnaPaymentTransfer;
use Generated\Shared\Transfer\QuoteTransfer;
use Orm\Zed\Klarna\Persistence\SpyPaymentKlarna;
use Orm\Zed\Klarna\Persistence\SpyPaymentKlarnaOrderItem;
use Orm\Zed\Klarna\Persistence\SpyPaymentKlarnaTransactionStatusLog;

/**
 * Class Saver
 *
 * @package SprykerEco\Zed\Klarna\Business\Order
 */
class Saver implements SaverInterface
{

    const TYPE_SAVE = 'save';

    /**
     * @param \Generated\Shared\Transfer\QuoteTransfer $quoteTransfer
     * @param \Generated\Shared\Transfer\CheckoutResponseTransfer $checkoutResponseTransfer
     *
     * @return void
     */
    public function saveOrderPayment(QuoteTransfer $quoteTransfer, CheckoutResponseTransfer $checkoutResponseTransfer)
    {
        $paymentEntity = $this->savePaymentForOrder(
            $quoteTransfer->getPayment()->getKlarna(),
            $checkoutResponseTransfer->getSaveOrder()->getIdSalesOrder()
        );

        $this->savePaymentForOrderItems(
            $checkoutResponseTransfer->getSaveOrder()->getOrderItems(),
            $paymentEntity->getIdPaymentKlarna()
        );

        $this->saveKlarnaPaymentStatusLog($paymentEntity->getIdPaymentKlarna());
    }

    /**
     * @param \Generated\Shared\Transfer\KlarnaPaymentTransfer $paymentTransfer
     * @param int $idSalesOrder
     *
     * @return \Orm\Zed\Klarna\Persistence\SpyPaymentKlarna
     */
    protected function savePaymentForOrder(KlarnaPaymentTransfer $paymentTransfer, $idSalesOrder)
    {
        $paymentEntity = new SpyPaymentKlarna();
        $addressTransfer = $paymentTransfer->getAddress();

        $formattedStreet = trim(
            sprintf(
                '%s %s %s',
                $addressTransfer->getAddress1(),
                $addressTransfer->getAddress2(),
                $addressTransfer->getAddress3()
            )
        );

        $paymentEntity->fromArray($addressTransfer->toArray());
        $paymentEntity->fromArray($paymentTransfer->toArray());

        $paymentEntity
            ->setStreet($formattedStreet)
            ->setCountryIso2Code($addressTransfer->getIso2Code())
            ->setFkSalesOrder($idSalesOrder);

        $paymentEntity->save();

        return $paymentEntity;
    }

    /**
     * @param \ArrayObject|\Generated\Shared\Transfer\ItemTransfer[] $orderItemTransfers
     * @param int $idPayment
     *
     * @return void
     */
    protected function savePaymentForOrderItems(\ArrayObject $orderItemTransfers, $idPayment)
    {
        foreach ($orderItemTransfers as $orderItemTransfer) {
            $paymentOrderItemEntity = new SpyPaymentKlarnaOrderItem();
            $paymentOrderItemEntity
                ->setFkPaymentKlarna($idPayment)
                ->setFkSalesOrderItem($orderItemTransfer->getIdSalesOrderItem());
            $paymentOrderItemEntity->save();
        }
    }

    /**
     * Save to klarna status log.
     *
     * @param int $idPaymentKlarna
     *
     * @return void
     */
    protected function saveKlarnaPaymentStatusLog($idPaymentKlarna)
    {
        $logEntity = new SpyPaymentKlarnaTransactionStatusLog();
        $logEntity->setProcessingType(self::TYPE_SAVE);
        $logEntity->setProcessingStatus('1');
        $logEntity->setFkPaymentKlarna($idPaymentKlarna);

        $logEntity->save();
    }
}
