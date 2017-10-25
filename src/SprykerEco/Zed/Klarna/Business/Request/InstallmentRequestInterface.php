<?php

namespace SprykerEco\Zed\Klarna\Business\Request;

use Generated\Shared\Transfer\QuoteTransfer;

interface InstallmentRequestInterface
{

    /**
     * @param \Generated\Shared\Transfer\QuoteTransfer $quoteTransfer
     *
     * @return \Generated\Shared\Transfer\KlarnaInstallmentResponseTransfer
     */
    public function getInstallments(QuoteTransfer $quoteTransfer);
}
