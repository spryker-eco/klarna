<?php

namespace SprykerEco\Zed\Klarna\Business\Log;

use Propel\Runtime\Collection\ObjectCollection;

interface LogInterface
{

    /**
     * @param \Propel\Runtime\Collection\ObjectCollection $orders
     *
     * @return array
     */
    public function getPaymentLogs(ObjectCollection $orders);
}
