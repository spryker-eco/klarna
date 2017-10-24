<?php

/**
 * MIT License
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace SprykerEco\Zed\Klarna\Business\Request;

use Generated\Shared\Transfer\KlarnaCheckoutServiceRequestTransfer;
use SprykerEco\Zed\Klarna\Business\Api\Handler\KlarnaApiInterface;
use SprykerEco\Zed\Klarna\Business\Response\Mapper\CheckoutServiceResponseTransferMapper;
use SprykerEco\Zed\Klarna\Business\Response\Mapper\CheckoutServiceResponseTransferMapperInterface;

/**
 * Class Installment
 *
 * @package SprykerEco\Zed\Klarna\Business\Payment
 */
class CheckoutServiceRequest
{
    /**
     * @var \SprykerEco\Zed\Klarna\Business\Api\Handler\KlarnaApiInterface
     */
    protected $klarnaApi;

    /**
     * @var \SprykerEco\Zed\Klarna\Business\Response\Mapper\CheckoutServiceResponseTransferMapperInterface
     */
    protected $installmentTransferMapper;

    /**
     * @param \SprykerEco\Zed\Klarna\Business\Api\Handler\KlarnaApiInterface $klarnaApi
     * @param \SprykerEco\Zed\Klarna\Business\Response\Mapper\CheckoutServiceResponseTransferMapperInterface $checkoutServiceResponseTransferMapper
     */
    public function __construct(
        KlarnaApiInterface $klarnaApi,
        CheckoutServiceResponseTransferMapperInterface $checkoutServiceResponseTransferMapper
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
