<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace SprykerEco\Zed\Klarna\Business\Order;

/**
 * Class SalesHelper
 *
 * @package SprykerEco\Zed\Klarna\Business\Order
 *
 * @author Daniel Bohnhardt <daniel.bohnhardt@twt.de>
 */
interface SalesHelperInterface
{

    /**
     * @author Daniel Bohnhardt <daniel.bohnhardt@twt.de>
     *
     * @param int $salesOrderId
     *
     * @return array
     */
    public function getKlarnaPaymentById($salesOrderId);

    /**
     * @author Daniel Bohnhardt <daniel.bohnhardt@twt.de>
     *
     * @param string $pdfUrlPattern
     * @param int $salesOrderId
     *
     * @return string
     */
    public function getInvoicePdfUrl($pdfUrlPattern, $salesOrderId);

}
