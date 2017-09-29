<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Zed\Klarna\Business\Request;

use Generated\Shared\Transfer\KlarnaCheckoutServiceRequestTransfer;
use Spryker\Zed\Klarna\Business\Api\Handler\KlarnaApi;
use Spryker\Zed\Klarna\Business\Response\Mapper\CheckoutServiceResponseTransferMapper;

/**
 * Class Installment
 *
 * @package Spryker\Zed\Klarna\Business\Payment
 */
class CheckoutServiceRequest
{

    /**
     * @var \Spryker\Zed\Klarna\Business\Api\Handler\KlarnaApi
     */
    protected $klarnaApi;

    /**
     * @var \Spryker\Zed\Klarna\Business\Response\Mapper\CheckoutServiceResponseTransferMapper
     */
    protected $installmentTransferMapper;

    /**
     * @param \Spryker\Zed\Klarna\Business\Api\Handler\KlarnaApi $klarnaApi
     * @param \Spryker\Zed\Klarna\Business\Response\Mapper\CheckoutServiceResponseTransferMapper $checkoutServiceResponseTransferMapper
     */
    public function __construct(
        KlarnaApi $klarnaApi,
        CheckoutServiceResponseTransferMapper $checkoutServiceResponseTransferMapper
    ) {
        $this->klarnaApi = $klarnaApi;
        $this->checkoutServiceResponseTransferMapper = $checkoutServiceResponseTransferMapper;
    }

    /**
     * @param \Generated\Shared\Transfer\KlarnaCheckoutServiceRequestTransfer $klarnaCheckoutServiceRequestTransfer
     *
     * @return \Generated\Shared\Transfer\KlarnaCheckoutServiceResponseTransfer
     */
    public function getInstallments(KlarnaCheckoutServiceRequestTransfer $klarnaCheckoutServiceRequestTransfer)
    {
        $checkoutServiceResponse = $this->klarnaApi->checkoutService($klarnaCheckoutServiceRequestTransfer);

        $checkoutServiceTransfer = $this->checkoutServiceResponseTransferMapper->map(
            $checkoutServiceResponse
        );

        return $checkoutServiceTransfer;
    }

}
