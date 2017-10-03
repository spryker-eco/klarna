<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace SprykerEco\Zed\Klarna\Dependency\Facade;

/**
 * Interface KlarnaToSales
 *
 * @package SprykerEco\Zed\Klarna\Dependency\Facade
 *
 * @author Daniel Bohnhardt <daniel.bohnhardt@twt.de>
 */
interface KlarnaToSalesInterface
{

    /**
     * @param int $idSalesOrder
     *
     * @return \Generated\Shared\Transfer\OrderTransfer
     */
    public function getOrderByIdSalesOrder($idSalesOrder);

}
