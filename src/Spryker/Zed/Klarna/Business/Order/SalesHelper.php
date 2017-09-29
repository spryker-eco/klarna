<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Zed\Klarna\Business\Order;

use Spryker\Zed\Klarna\Persistence\KlarnaQueryContainerInterface;

/**
 * Class SalesHelper
 *
 * @package Spryker\Zed\Klarna\Business\Order
 *
 * @author Daniel Bohnhardt <daniel.bohnhardt@twt.de>
 */
class SalesHelper implements SalesHelperInterface
{

    /**
     * @var \Spryker\Zed\Klarna\Persistence\KlarnaQueryContainerInterface
     *
     * @author Daniel Bohnhardt <daniel.bohnhardt@twt.de>
     */
    protected $queryContainer;

    /**
     * SalesHelper constructor.
     *
     * @param \Spryker\Zed\Klarna\Persistence\KlarnaQueryContainerInterface $queryContainer
     *
     * @author Daniel Bohnhardt <daniel.bohnhardt@twt.de>
     */
    public function __construct(KlarnaQueryContainerInterface $queryContainer)
    {
        $this->queryContainer = $queryContainer;
    }

    /**
     * @param int $salesOrderId
     *
     * @return array
     * @author Daniel Bohnhardt <daniel.bohnhardt@twt.de>
     */
    public function getKlarnaPaymentById($salesOrderId)
    {
        return $this->queryContainer->queryPaymentBySalesOrderId($salesOrderId)->find()->getData();
    }

    /**
     * @param string $pdfUrlPattern
     * @param int $salesOrderId
     *
     * @return string
     * @author Daniel Bohnhardt <daniel.bohnhardt@twt.de>
     */
    public function getInvoicePdfUrl($pdfUrlPattern, $salesOrderId)
    {
        return sprintf($pdfUrlPattern, $salesOrderId);
    }

}
