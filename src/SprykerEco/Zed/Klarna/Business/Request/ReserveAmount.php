<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace SprykerEco\Zed\Klarna\Business\Request;

use Generated\Shared\Transfer\KlarnaReserveAmountResponseTransfer;
use Generated\Shared\Transfer\QuoteTransfer;
use SprykerEco\Zed\Klarna\Business\Api\Handler\KlarnaApi;

/**
 * Class ReserveAmount
 *
 * @package SprykerEco\Zed\Klarna\Business\Payment
 *
 * @author Daniel Bohnhardt <daniel.bohnhardt@twt.de>
 */
class ReserveAmount
{

    const RESERVE_AMOUNT_KEY_REF_NO = 0;
    const RESERVE_AMOUNT_KEY_STATUS = 1;
    const RESERVE_AMOUNT_KEY_ERROR = 2;

    /**
     * @var \SprykerEco\Zed\Klarna\Business\Api\Handler\KlarnaApi $klarnaApi
     */
    protected $klarnaApi;

    /**
     * ReserveAmount constructor.
     *
     * @author Daniel Bohnhardt <daniel.bohnhardt@twt.de>
     *
     * @param \SprykerEco\Zed\Klarna\Business\Api\Handler\KlarnaApi $klarnaApi
     */
    public function __construct(KlarnaApi $klarnaApi)
    {
        $this->klarnaApi = $klarnaApi;
    }

    /**
     * @author Daniel Bohnhardt <daniel.bohnhardt@twt.de>
     *
     * @param \Generated\Shared\Transfer\QuoteTransfer $quoteTransfer
     *
     * @return \Generated\Shared\Transfer\KlarnaReserveAmountResponseTransfer
     */
    public function reserveAmount(QuoteTransfer $quoteTransfer)
    {
        $apiResult = $this->klarnaApi->reserveAmount($quoteTransfer);

        $reserveAmountTransfer = new KlarnaReserveAmountResponseTransfer();
        $reserveAmountTransfer->setStatus($apiResult[self::RESERVE_AMOUNT_KEY_STATUS]);
        $reserveAmountTransfer->setReservationNo($apiResult[self::RESERVE_AMOUNT_KEY_REF_NO]);
        if (isset($apiResult[self::RESERVE_AMOUNT_KEY_ERROR])) {
            $reserveAmountTransfer->setError($apiResult[self::RESERVE_AMOUNT_KEY_ERROR]);
        }

        return $reserveAmountTransfer;
    }

    /**
     * @author Daniel Bohnhardt <daniel.bohnhardt@twt.de>
     *
     * @param \Generated\Shared\Transfer\QuoteTransfer $quoteTransfer
     *
     * @return \Generated\Shared\Transfer\KlarnaReserveAmountResponseTransfer
     */
    public function updateReservation(QuoteTransfer $quoteTransfer)
    {
        $apiResult = $this->klarnaApi->update($quoteTransfer);

        $reserveAmountTransfer = new KlarnaReserveAmountResponseTransfer();
        if ($apiResult === KlarnaApi::UPDATE_SUCCESS) {
            $reserveAmountTransfer->setStatus(1);
        } else {
            $reserveAmountTransfer->setStatus(0);
            $reserveAmountTransfer->setError($apiResult);
        }

        return $reserveAmountTransfer;
    }

}
