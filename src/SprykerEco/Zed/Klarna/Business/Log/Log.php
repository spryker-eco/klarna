<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace SprykerEco\Zed\Klarna\Business\Log;

use Propel\Runtime\Collection\ObjectCollection;
use SprykerEco\Zed\Klarna\Persistence\KlarnaQueryContainerInterface;

/**
 * Class Log
 *
 * @package SprykerEco\Zed\Klarna\Business\Payment
 *
 * @author Daniel Bohnhardt <daniel.bohnhardt@twt.de>
 */
class Log
{

    /**
     * @var \SprykerEco\Zed\Klarna\Persistence\KlarnaQueryContainerInterface
     *
     * @author Daniel Bohnhardt <daniel.bohnhardt@twt.de>
     */
    protected $queryContainer;

    /**
     * Log constructor.
     *
     * @param \SprykerEco\Zed\Klarna\Persistence\KlarnaQueryContainerInterface $queryContainer
     *
     * @author Daniel Bohnhardt <daniel.bohnhardt@twt.de>
     */
    public function __construct(KlarnaQueryContainerInterface $queryContainer)
    {
        $this->queryContainer = $queryContainer;
    }

    /**
     * @param \Propel\Runtime\Collection\ObjectCollection $orders
     *
     * @return array
     * @author Daniel Bohnhardt <daniel.bohnhardt@twt.de>
     */
    public function getPaymentLogs(ObjectCollection $orders)
    {
        $paymentLogs = $this->queryContainer->createApiLogsByOrderIds($orders)->find()->getData();

        $log = [];

        /** @var \Orm\Zed\Klarna\Persistence\Base\SpyPaymentKlarnaTransactionStatusLog $paymentLog */
        foreach ($paymentLogs as $paymentLog) {
            $log[] = [
                'logType'       => get_class($paymentLog),
                'CreatedAt'     => $paymentLog->getCreatedAt(),
                'Status'        => $paymentLog->getProcessingType(),
                'TransactionId' => '',
                'Request'       => $paymentLog->getProcessingErrorMessage()
            ];
        }

        return $log;
    }

}
