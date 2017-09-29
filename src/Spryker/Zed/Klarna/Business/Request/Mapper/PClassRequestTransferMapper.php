<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Zed\Klarna\Business\Request\Mapper;

use Generated\Shared\Transfer\KlarnaObjectInitTransfer;
use Generated\Shared\Transfer\KlarnaPClassRequestTransfer;
use Generated\Shared\Transfer\QuoteTransfer;

class PClassRequestTransferMapper
{

    /**
     * @param \Generated\Shared\Transfer\KlarnaPClassRequestTransfer $klarnaPClassRequestTransfer
     * @param \Generated\Shared\Transfer\QuoteTransfer $quoteTransfer
     *
     * @return \Generated\Shared\Transfer\KlarnaPClassRequestTransfer
     */
    public function map(
        KlarnaPClassRequestTransfer $klarnaPClassRequestTransfer,
        QuoteTransfer $quoteTransfer
    ) {
        $paymentTransfer = $quoteTransfer->getPayment()->getKlarna();

        $klarnaObjectInitTransfer = new KlarnaObjectInitTransfer();
        $klarnaObjectInitTransfer->setCurrencyIso3Code($paymentTransfer->getCurrencyIso3Code());
        $klarnaObjectInitTransfer->setIso2Code($paymentTransfer->getLanguageIso2Code());
        $klarnaObjectInitTransfer->setClientIp($paymentTransfer->getClientIp());

        $klarnaPClassRequestTransfer->setKlarnaObjectInit($klarnaObjectInitTransfer);
        $klarnaPClassRequestTransfer->setPClassType($quoteTransfer->getKlarnaPClassType());

        return $klarnaPClassRequestTransfer;
    }

}
