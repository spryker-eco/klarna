<?php

/**
 * MIT License
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace SprykerEco\Zed\Klarna\Business\Request;

use Generated\Shared\Transfer\KlarnaCheckoutServiceRequestTransfer;
use SprykerEco\Zed\Klarna\Business\Api\Handler\KlarnaApi;
use SprykerEco\Zed\Klarna\Business\Response\Mapper\CheckoutServiceResponseTransferMapper;

/**
 * Class Installment
 *
 * @package SprykerEco\Zed\Klarna\Business\Payment
 */
class CheckoutServiceRequest
{

    /**
     * @var \SprykerEco\Zed\Klarna\Business\Api\Handler\KlarnaApi
     */
    protected $klarnaApi;

    /**
     * @var \SprykerEco\Zed\Klarna\Business\Response\Mapper\CheckoutServiceResponseTransferMapper
     */
    protected $installmentTransferMapper;

    /**
     * @param \SprykerEco\Zed\Klarna\Business\Api\Handler\KlarnaApi $klarnaApi
     * @param \SprykerEco\Zed\Klarna\Business\Response\Mapper\CheckoutServiceResponseTransferMapper $checkoutServiceResponseTransferMapper
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
