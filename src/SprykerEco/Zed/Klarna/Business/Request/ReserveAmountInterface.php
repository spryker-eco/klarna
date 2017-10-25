<?php

namespace SprykerEco\Zed\Klarna\Business\Request;

use Generated\Shared\Transfer\QuoteTransfer;

interface ReserveAmountInterface
{

    /**
     * @param \Generated\Shared\Transfer\QuoteTransfer $quoteTransfer
     *
     * @return \Generated\Shared\Transfer\KlarnaReserveAmountResponseTransfer
     */
    public function createReserveAmountTransfer(QuoteTransfer $quoteTransfer);

    /**
     * @param \Generated\Shared\Transfer\QuoteTransfer $quoteTransfer
     *
     * @return \Generated\Shared\Transfer\KlarnaReserveAmountResponseTransfer
     */
    public function createUpdateReserveAmountTransfer(QuoteTransfer $quoteTransfer);
}
