<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Zed\Klarna\Business\Order;

/**
 * Class SalesHelper
 *
 * @package Spryker\Zed\Klarna\Business\Order
 *
 * @author Daniel Bohnhardt <daniel.bohnhardt@twt.de>
 */
interface SalesHelperInterface
{

    /**
     * @param int $salesOrderId
     *
     * @return array
     * @author Daniel Bohnhardt <daniel.bohnhardt@twt.de>
     */
    public function getKlarnaPaymentById($salesOrderId);

    /**
     * @param string $pdfUrlPattern
     * @param int $salesOrderId
     *
     * @return string
     * @author Daniel Bohnhardt <daniel.bohnhardt@twt.de>
     */
    public function getInvoicePdfUrl($pdfUrlPattern, $salesOrderId);

}
