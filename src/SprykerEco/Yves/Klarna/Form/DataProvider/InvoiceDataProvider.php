<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace SprykerEco\Yves\Klarna\Form\DataProvider;

use Generated\Shared\Transfer\KlarnaPaymentTransfer;
use Generated\Shared\Transfer\PaymentTransfer;
use Spryker\Shared\Transfer\AbstractTransfer;
use Spryker\Yves\StepEngine\Dependency\Form\StepEngineFormDataProviderInterface;

/**
 * Class InvoiceDataProvider
 *
 * @author Daniel Bohnhardt <daniel.bohnhardt@twt.de>
 */
class InvoiceDataProvider implements StepEngineFormDataProviderInterface
{

    /**
     * @param \Spryker\Shared\Transfer\AbstractTransfer|\Generated\Shared\Transfer\QuoteTransfer $quoteTransfer
     *
     * @return \Generated\Shared\Transfer\QuoteTransfer
     * @author Daniel Bohnhardt <daniel.bohnhardt@twt.de>
     */
    public function getData(AbstractTransfer $quoteTransfer)
    {
        if (empty($quoteTransfer->getPayment())) {
            $paymentTransfer = new PaymentTransfer();
            $paymentTransfer->setKlarna(new KlarnaPaymentTransfer());
            $paymentTransfer->setKlarnaInvoice(new KlarnaPaymentTransfer());
            $quoteTransfer->setPayment($paymentTransfer);
        }

        return $quoteTransfer;
    }

    /**
     * @param \Spryker\Shared\Transfer\AbstractTransfer|\Generated\Shared\Transfer\QuoteTransfer $quoteTransfer
     *
     * @return array
     * @author Daniel Bohnhardt <daniel.bohnhardt@twt.de>
     */
    public function getOptions(AbstractTransfer $quoteTransfer)
    {
        return [];
    }

}
