<?php
/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */
namespace Spryker\Zed\Klarna\Business\Address;

use Generated\Shared\Transfer\AddressTransfer;
use Generated\Shared\Transfer\OrderTransfer;
use Orm\Zed\Klarna\Persistence\SpyPaymentKlarna;
use Spryker\Zed\Klarna\Business\Api\Handler\KlarnaApi;

/**
 * AddressUpdater
 *
 * @package Spryker\Zed\Klarna\Business\Address
 */
class AddressUpdater
{

    /**
     * @var \Spryker\Zed\Klarna\Business\Api\Handler\KlarnaApi
     */
    protected $klarnaApi;

    /**
     * @var \Orm\Zed\Klarna\Persistence\SpyPaymentKlarna
     */
    protected $spyPayment;

    /**
     * @param \Spryker\Zed\Klarna\Business\Api\Handler\KlarnaApi $klarnaApi
     * @param \Orm\Zed\Klarna\Persistence\SpyPaymentKlarna $spyPayment
     */
    public function __construct(KlarnaApi $klarnaApi, SpyPaymentKlarna $spyPayment)
    {
        $this->klarnaApi = $klarnaApi;
        $this->spyPayment = $spyPayment;
    }

    /**
     * @param \Generated\Shared\Transfer\AddressTransfer $addressTransfer
     * @param \Generated\Shared\Transfer\OrderTransfer $salesOrderTransfer
     *
     * @return boolean
     */
    public function update(AddressTransfer $addressTransfer, OrderTransfer $salesOrderTransfer)
    {
        $this->klarnaApi->updateAddress($addressTransfer, $salesOrderTransfer, $this->spyPayment);

        return true;
    }

}
