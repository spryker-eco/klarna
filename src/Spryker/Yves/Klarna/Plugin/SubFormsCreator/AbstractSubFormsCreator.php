<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Yves\Klarna\Plugin\SubFormsCreator;

use Generated\Shared\Transfer\QuoteTransfer;
use Spryker\Yves\Klarna\Plugin\KlarnaInstallmentSubFormPlugin;
use Spryker\Yves\Klarna\Plugin\KlarnaInvoiceSubFormPlugin;

abstract class AbstractSubFormsCreator
{

    /**
     * @param \Generated\Shared\Transfer\QuoteTransfer
     *
     * @return \Spryker\Yves\Klarna\Plugin\KlarnaInstallmentSubFormPlugin
     */
    protected function createKlarnaInstallmentSubFormPlugin(QuoteTransfer $quoteTransfer)
    {
        return new KlarnaInstallmentSubFormPlugin($quoteTransfer->getBillingAddress()->getIso2Code(), $quoteTransfer);
    }

    /**
     * @param \Generated\Shared\Transfer\QuoteTransfer
     *
     * @return \Spryker\Yves\Klarna\Plugin\KlarnaInvoiceSubFormPlugin
     */
    protected function createKlarnaInvoiceSubFormPlugin(QuoteTransfer $quoteTransfer)
    {
        return new KlarnaInvoiceSubFormPlugin($quoteTransfer->getBillingAddress()->getIso2Code());
    }

}
