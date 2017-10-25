<?php

/**
 * MIT License
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace SprykerEco\Yves\Klarna\Plugin;

use Generated\Shared\Transfer\QuoteTransfer;
use Spryker\Shared\Kernel\Transfer\AbstractTransfer;
use Spryker\Yves\Kernel\AbstractPlugin;
use Spryker\Yves\StepEngine\Dependency\Plugin\Handler\StepHandlerPluginInterface;
use SprykerEco\Yves\Klarna\Handler\Exception\KlarnaHandlerException;
use Symfony\Component\HttpFoundation\Request;

/**
 * @method \SprykerEco\Yves\Klarna\KlarnaFactory getFactory()
 */
class KlarnaHandlerPlugin extends AbstractPlugin implements StepHandlerPluginInterface
{
    /**
     * @param \Symfony\Component\HttpFoundation\Request $request
     * @param \Generated\Shared\Transfer\QuoteTransfer|\Spryker\Shared\Kernel\Transfer\AbstractTransfer $quoteTransfer
     *
     * @return \Generated\Shared\Transfer\QuoteTransfer
     */
    public function addToDataClass(Request $request, AbstractTransfer $quoteTransfer)
    {
        return $this->addToQuote($request, $quoteTransfer);
    }

    /**
     * @param \Symfony\Component\HttpFoundation\Request $request
     * @param \Generated\Shared\Transfer\QuoteTransfer $quoteTransfer
     *
     * @return \Generated\Shared\Transfer\QuoteTransfer
     */
    public function addToQuote(Request $request, QuoteTransfer $quoteTransfer)
    {
        try {
            return $this->getFactory()
                ->createKlarnaHandler()
                ->addPaymentToQuote($request, $quoteTransfer);
        } catch (KlarnaHandlerException $e) {
            $quoteTransfer->setPayment(null);
            $this->getFactory()
                ->getFlashMessenger()
                ->addErrorMessage(
                    $this->getFactory()->getTranslatorClient()->translate(
                        $e->getMessage(),
                        $this->getFactory()->getStore()->getCurrentLocale()
                    )
                );
        }

        return $quoteTransfer;
    }
}
