<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Zed\Klarna\Persistence;

/**
 * Class KlarnaQueryContainer
 *
 * @package Spryker\Zed\Klarna\Persistence
 *
 * @author Daniel Bohnhardt <daniel.bohnhardt@twt.de>
 */
interface KlarnaQueryContainerInterface
{

    /**
     * @api
     * @return \Orm\Zed\Klarna\Persistence\SpyPaymentKlarnaQuery
     */
    public function queryPayments();

    /**
     * @api
     * @param int $idPayment
     *
     * @return \Orm\Zed\Klarna\Persistence\SpyPaymentKlarnaQuery
     */
    public function queryPaymentById($idPayment);

    /**
     * @api
     * @param int $idSalesOrder
     *
     * @return \Orm\Zed\Klarna\Persistence\SpyPaymentKlarnaQuery
     */
    public function queryPaymentBySalesOrderId($idSalesOrder);

    /**
     * @api
     * @return \Orm\Zed\Klarna\Persistence\SpyPaymentKlarnaTransactionStatusLogQuery
     */
    public function queryTransactionStatusLog();

    /**
     * @api
     * @param int $idPayment
     *
     * @return \Orm\Zed\Klarna\Persistence\SpyPaymentKlarnaTransactionStatusLogQuery
     */
    public function queryTransactionStatusLogByPaymentId($idPayment);

}
