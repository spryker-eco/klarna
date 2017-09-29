<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Yves\Klarna\Plugin;

use Generated\Shared\Transfer\QuoteTransfer;
use Spryker\Shared\Kernel\Store;
use Spryker\Shared\Transfer\AbstractTransfer;
use Spryker\Yves\Kernel\AbstractPlugin;
use Spryker\Yves\Klarna\Handler\Exception\KlarnaHandlerException;
use Spryker\Yves\StepEngine\Dependency\Plugin\Handler\StepHandlerPluginInterface;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class KlarnaHandlerPlugin
 *
 * @package Spryker\Yves\Klarna\Plugin
 * @method \Spryker\Yves\Klarna\KlarnaFactory getFactory()
 * @author Daniel Bohnhardt <daniel.bohnhardt@twt.de>
 */
class KlarnaHandlerPlugin extends AbstractPlugin implements StepHandlerPluginInterface
{

    /**
     * @param \Symfony\Component\HttpFoundation\Request $request
     * @param \Generated\Shared\Transfer\QuoteTransfer|\Spryker\Shared\Transfer\AbstractTransfer $quoteTransfer
     *
     * @return \Generated\Shared\Transfer\QuoteTransfer
     */
    public function addToDataClass(Request $request, AbstractTransfer $quoteTransfer)
    {
        $this->addToQuote($request, $quoteTransfer);
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
            $klarnaHandler = $this->getFactory()->createKlarnaHandler();
            $klarnaHandler->addPaymentToQuote($request, $quoteTransfer);
        } catch (KlarnaHandlerException $e) {
            $factory = $this->getFactory();
            $quoteTransfer->setPayment(null);
            $factory->getFlashMessenger()
                ->addErrorMessage(
                    $factory->getTranslatorClient()->translate(
                        $e->getMessage(),
                        $factory->getStore()->getCurrentLocale()
                    )
                );
        }
    }

    /**
     * @return \Spryker\Client\Glossary\GlossaryClient
     * @author Daniel Bohnhardt <daniel.bohnhardt@twt.de>
     */
    public function getTranslatorClient()
    {
        return $this->getLocator()->glossary()->client();
    }

    /**
     * @return \Spryker\Shared\Kernel\Store
     * @author Daniel Bohnhardt <daniel.bohnhardt@twt.de>
     */
    public function getStore()
    {
        return Store::getInstance();
    }

}
