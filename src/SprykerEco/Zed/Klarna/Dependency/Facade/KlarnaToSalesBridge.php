<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace SprykerEco\Zed\Klarna\Dependency\Facade;

/**
 * Class KlarnaToSalesBridge
 *
 * @package SprykerEco\Zed\Klarna\Dependency\Facade
 *
 * @author Daniel Bohnhardt <daniel.bohnhardt@twt.de>
 */
class KlarnaToSalesBridge implements KlarnaToSalesInterface
{

    /**
     * @var \Spryker\Zed\SalesAggregator\Business\SalesAggregatorFacade
     */
    protected $salesAggregationFacade;

    /**
     * @param \Spryker\Zed\SalesAggregator\Business\SalesAggregatorFacade $salesAggregationFacade
     */
    public function __construct($salesAggregationFacade)
    {
        $this->salesAggregationFacade = $salesAggregationFacade;
    }

    /**
     * @param int $idSalesOrder
     *
     * @return \Generated\Shared\Transfer\OrderTransfer
     */
    public function getOrderTotalsByIdSalesOrder($idSalesOrder)
    {
        return $this->salesAggregationFacade->getOrderTotalsByIdSalesOrder($idSalesOrder);
    }

}
