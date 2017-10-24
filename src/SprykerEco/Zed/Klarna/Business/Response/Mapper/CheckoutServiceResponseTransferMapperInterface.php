<?php

namespace SprykerEco\Zed\Klarna\Business\Response\Mapper;

use CheckoutServiceResponse;

interface CheckoutServiceResponseTransferMapperInterface
{

    /**
     * @param \CheckoutServiceResponse $checkoutServiceResponse
     *
     * @return \Generated\Shared\Transfer\KlarnaCheckoutServiceResponseTransfer
     */
    public function map(CheckoutServiceResponse $checkoutServiceResponse);
}
