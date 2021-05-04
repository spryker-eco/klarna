<?php

/**
 * MIT License
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace SprykerEco\Zed\Klarna\Persistence;

use Orm\Zed\Klarna\Persistence\SpyPaymentKlarnaQuery;
use Orm\Zed\Klarna\Persistence\SpyPaymentKlarnaTransactionStatusLogQuery;
use Spryker\Zed\Kernel\Persistence\AbstractQueryContainer;

/**
 * Class KlarnaQueryContainer
 *
 * @package SprykerEco\Zed\Klarna\Persistence
 *
 * @author Daniel Bohnhardt <daniel.bohnhardt@twt.de>
 */
class KlarnaQueryContainer extends AbstractQueryContainer implements KlarnaQueryContainerInterface
{

    /**
     * @api
     *
     * @return \Orm\Zed\Klarna\Persistence\SpyPaymentKlarnaQuery
     */
    public function queryPayments()
    {
        return SpyPaymentKlarnaQuery::create();
    }

    /**
     * @api
     *
     * @param int $idPayment
     *
     * @return \Orm\Zed\Klarna\Persistence\SpyPaymentKlarnaQuery
     */
    public function queryPaymentById($idPayment)
    {
        return $this
            ->queryPayments()
            ->filterByIdPaymentKlarna($idPayment);
    }

    /**
     * @api
     *
     * @param int $idSalesOrder
     *
     * @return \Orm\Zed\Klarna\Persistence\SpyPaymentKlarnaQuery
     */
    public function queryPaymentBySalesOrderId($idSalesOrder)
    {
        return $this
            ->queryPayments()
            ->filterByFkSalesOrder($idSalesOrder);
    }

    /**
     * @api
     *
     * @return \Orm\Zed\Klarna\Persistence\SpyPaymentKlarnaTransactionStatusLogQuery
     */
    public function queryTransactionStatusLog()
    {
        return SpyPaymentKlarnaTransactionStatusLogQuery::create();
    }

    /**
     * @api
     *
     * @param int $idPayment
     *
     * @return \Orm\Zed\Klarna\Persistence\SpyPaymentKlarnaTransactionStatusLogQuery
     */
    public function queryTransactionStatusLogByPaymentId($idPayment)
    {
        return $this
            ->queryTransactionStatusLog()
            ->filterByFkPaymentKlarna($idPayment);
    }

}
