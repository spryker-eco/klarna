<?php

/**
 * MIT License
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace SprykerEco\Zed\Klarna\Business\Request;

use Generated\Shared\Transfer\KlarnaReserveAmountResponseTransfer;
use Generated\Shared\Transfer\QuoteTransfer;
use SprykerEco\Zed\Klarna\Business\Api\Handler\KlarnaApi;
use SprykerEco\Zed\Klarna\Business\Api\Handler\KlarnaApiInterface;

class ReserveAmount implements ReserveAmountInterface
{
    const RESERVE_AMOUNT_KEY_REF_NO = 0;
    const RESERVE_AMOUNT_KEY_STATUS = 1;
    const RESERVE_AMOUNT_KEY_ERROR = 2;

    /**
     * @var \SprykerEco\Zed\Klarna\Business\Api\Handler\KlarnaApiInterface $klarnaApi
     */
    protected $klarnaApi;

    /**
     * @param \SprykerEco\Zed\Klarna\Business\Api\Handler\KlarnaApiInterface $klarnaApi
     */
    public function __construct(KlarnaApiInterface $klarnaApi)
    {
        $this->klarnaApi = $klarnaApi;
    }

    /**
     * @param \Generated\Shared\Transfer\QuoteTransfer $quoteTransfer
     *
     * @return \Generated\Shared\Transfer\KlarnaReserveAmountResponseTransfer
     */
    public function createReserveAmountTransfer(QuoteTransfer $quoteTransfer)
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
     * @param \Generated\Shared\Transfer\QuoteTransfer $quoteTransfer
     *
     * @return \Generated\Shared\Transfer\KlarnaReserveAmountResponseTransfer
     */
    public function createUpdateReserveAmountTransfer(QuoteTransfer $quoteTransfer)
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
