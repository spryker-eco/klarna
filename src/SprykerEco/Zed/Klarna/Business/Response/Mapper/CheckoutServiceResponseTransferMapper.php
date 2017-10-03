<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace SprykerEco\Zed\Klarna\Business\Response\Mapper;

use CheckoutServiceResponse;
use Generated\Shared\Transfer\KlarnaCheckoutServiceResponseTransfer;
use Generated\Shared\Transfer\KlarnaPaymentMethodDetailTransfer;
use Generated\Shared\Transfer\KlarnaPaymentMethodTransfer;

class CheckoutServiceResponseTransferMapper
{

    /**
     * @param \CheckoutServiceResponse $checkoutServiceResponse
     *
     * @return \Generated\Shared\Transfer\KlarnaCheckoutServiceResponseTransfer
     */
    public function map(CheckoutServiceResponse $checkoutServiceResponse)
    {
        $checkoutServiceTransfer = new KlarnaCheckoutServiceResponseTransfer();
        $responseData = $checkoutServiceResponse->getData();
        if (isset($responseData['http_status_code']) && $responseData['http_status_code'] == 400) {
            $checkoutServiceTransfer->setHttpStatusCode($responseData['http_status_code']);
            $checkoutServiceTransfer->setInternalMessage($responseData['internal_message']);
            $checkoutServiceTransfer->setPublicCode($responseData['public_code']);

            return $checkoutServiceTransfer;
        }

        $checkoutServiceTransfer->setHttpStatusCode(200);

        foreach ($responseData as $paymentMethods) {
            foreach ($paymentMethods as $paymentMethod) {
                $paymentMethodTransfer = new KlarnaPaymentMethodTransfer();
                $paymentMethodTransfer->setPclassId($paymentMethod['pclass_id']);
                $paymentMethodTransfer->setName($paymentMethod['name']);
                $paymentMethodTransfer->setLogoUrl($paymentMethod['logo']['uri']);
                $paymentMethodTransfer->setTermsUrl($paymentMethod['terms']['uri']);
                $paymentMethodTransfer->setGroupTitle($paymentMethod['group']['title']);
                $paymentMethodTransfer->setGroupCode($paymentMethod['group']['code']);
                $paymentMethodTransfer->setTitle($paymentMethod['title']);
                $paymentMethodTransfer->setExtraInfo($paymentMethod['extra_info']);
                $paymentMethodTransfer->setUseCase($paymentMethod['use_case']);

                foreach ($paymentMethod['details'] as $detailName => $detail) {
                    $paymentMethodTransferDetail = new KlarnaPaymentMethodDetailTransfer();
                    $paymentMethodTransferDetail->setName($detailName);
                    $paymentMethodTransferDetail->setLabel($detail['label']);
                    $paymentMethodTransferDetail->setValue($detail['value']);
                    if (isset($detail['symbol'])) {
                        $paymentMethodTransferDetail->setSymbol($detail['symbol']);
                    }

                    $paymentMethodTransfer->addKlarnaPaymentMethodDetail($paymentMethodTransferDetail);
                }

                $checkoutServiceTransfer->addKlarnaPaymentMethod($paymentMethodTransfer);
            }
        }

        return $checkoutServiceTransfer;
    }

}
