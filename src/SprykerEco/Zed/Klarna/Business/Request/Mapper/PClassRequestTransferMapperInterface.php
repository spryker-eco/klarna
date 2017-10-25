<?php

/**
 * MIT License
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace SprykerEco\Zed\Klarna\Business\Request\Mapper;

use Generated\Shared\Transfer\KlarnaPClassRequestTransfer;
use Generated\Shared\Transfer\QuoteTransfer;

interface PClassRequestTransferMapperInterface
{
    /**
     * @param \Generated\Shared\Transfer\KlarnaPClassRequestTransfer $klarnaPClassRequestTransfer
     * @param \Generated\Shared\Transfer\QuoteTransfer $quoteTransfer
     *
     * @return \Generated\Shared\Transfer\KlarnaPClassRequestTransfer
     */
    public function map(KlarnaPClassRequestTransfer $klarnaPClassRequestTransfer, QuoteTransfer $quoteTransfer);
}
