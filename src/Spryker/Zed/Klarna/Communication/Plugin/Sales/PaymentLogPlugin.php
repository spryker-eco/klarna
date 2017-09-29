<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Zed\Klarna\Communication\Plugin\Sales;

use Propel\Runtime\Collection\ObjectCollection;
use Spryker\Zed\Kernel\Communication\AbstractPlugin;

/**
 * Class PaymentLog
 *
 * @package Spryker\Zed\Klarna\Communication\Plugin\Sales
 *
 * @author Daniel Bohnhardt <daniel.bohnhardt@twt.de>
 * @method \Spryker\Zed\Klarna\Business\KlarnaFacade getFacade()
 * @method \Spryker\Zed\Klarna\Business\KlarnaBusinessFactory getFactory()
 */
class PaymentLogPlugin extends AbstractPlugin
{

    /**
     * @param \Propel\Runtime\Collection\ObjectCollection $orders
     *
     * @return array
     */
    public function getPaymentLogs(ObjectCollection $orders)
    {
        return $this->getFacade()->getPaymentLogs($orders);
    }

}
