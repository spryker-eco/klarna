<?php

/**
 * MIT License
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace SprykerEco\Zed\Klarna\Business\Order;

interface SalesHelperInterface
{
    /**
     * @param int $salesOrderId
     *
     * @return array
     */
    public function getKlarnaPaymentById($salesOrderId);

    /**
     * @param int $salesOrderId
     *
     * @return string
     */
    public function getInvoicePdfUrl($salesOrderId);
}
