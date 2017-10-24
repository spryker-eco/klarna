<?php

namespace SprykerEco\Zed\Klarna\Business\Response\Mapper;

use Generated\Shared\Transfer\QuoteTransfer;

interface InstallmentTransferMapperInterface
{

    /**
     * @param \Generated\Shared\Transfer\QuoteTransfer $quoteTransfer
     * @param \KlarnaPClass[] $pClasses
     *
     * @return \Generated\Shared\Transfer\KlarnaInstallmentResponseTransfer
     */
    public function map(QuoteTransfer $quoteTransfer, array $pClasses);
}
