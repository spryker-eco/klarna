<?php

/**
 * MIT License
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace SprykerEco\Zed\Klarna\Business\Order;

use SprykerEco\Zed\Klarna\KlarnaConfig;
use SprykerEco\Zed\Klarna\Persistence\KlarnaQueryContainerInterface;

class SalesHelper implements SalesHelperInterface
{
    /**
     * @var \SprykerEco\Zed\Klarna\Persistence\KlarnaQueryContainerInterface
     */
    protected $queryContainer;

    /**
     * @var KlarnaConfig
     */
    protected $config;

    /**
     * SalesHelper constructor.
     *
     * @param \SprykerEco\Zed\Klarna\Persistence\KlarnaQueryContainerInterface $queryContainer
     */
    public function __construct(KlarnaQueryContainerInterface $queryContainer, KlarnaConfig $config)
    {
        $this->queryContainer = $queryContainer;
        $this->config = $config;
    }

    /**
     * @param int $salesOrderId
     *
     * @return array
     */
    public function getKlarnaPaymentById($salesOrderId)
    {
        return $this->queryContainer->queryPaymentBySalesOrderId($salesOrderId)->find()->getData();
    }

    /**
     * @param int $salesOrderId
     *
     * @return string
     */
    public function getInvoicePdfUrl($salesOrderId)
    {
        return sprintf($this->config->getPdfUrlPattern(), $salesOrderId);
    }
}
