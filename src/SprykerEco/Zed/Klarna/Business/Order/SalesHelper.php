<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace SprykerEco\Zed\Klarna\Business\Order;

use SprykerEco\Zed\Klarna\Persistence\KlarnaQueryContainerInterface;

/**
 * Class SalesHelper
 *
 * @package SprykerEco\Zed\Klarna\Business\Order
 *
 * @author Daniel Bohnhardt <daniel.bohnhardt@twt.de>
 */
class SalesHelper implements SalesHelperInterface
{

    /**
     * @var \SprykerEco\Zed\Klarna\Persistence\KlarnaQueryContainerInterface
     *
     * @author Daniel Bohnhardt <daniel.bohnhardt@twt.de>
     */
    protected $queryContainer;

    /**
     * SalesHelper constructor.
     *
     * @author Daniel Bohnhardt <daniel.bohnhardt@twt.de>
     *
     * @param \SprykerEco\Zed\Klarna\Persistence\KlarnaQueryContainerInterface $queryContainer
     */
    public function __construct(KlarnaQueryContainerInterface $queryContainer)
    {
        $this->queryContainer = $queryContainer;
    }

    /**
     * @author Daniel Bohnhardt <daniel.bohnhardt@twt.de>
     *
     * @param int $salesOrderId
     *
     * @return array
     */
    public function getKlarnaPaymentById($salesOrderId)
    {
        return $this->queryContainer->queryPaymentBySalesOrderId($salesOrderId)->find()->getData();
    }

    /**
     * @author Daniel Bohnhardt <daniel.bohnhardt@twt.de>
     *
     * @param string $pdfUrlPattern
     * @param int $salesOrderId
     *
     * @return string
     */
    public function getInvoicePdfUrl($pdfUrlPattern, $salesOrderId)
    {
        return sprintf($pdfUrlPattern, $salesOrderId);
    }

}
