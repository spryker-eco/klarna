<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace SprykerEco\Yves\Klarna\Plugin;

use Spryker\Yves\Kernel\AbstractPlugin;
use Spryker\Yves\StepEngine\Dependency\Plugin\Form\SubFormPluginInterface;

/**
 * Class KlarnaRateSubFormPlugin
 *
 * @package SprykerEco\Yves\Klarna\Plugin
 * @method \SprykerEco\Yves\Klarna\KlarnaFactory getFactory()
 *
 * @author Daniel Bohnhardt <daniel.bohnhardt@twt.de>
 */
class KlarnaInstallmentSubFormPlugin extends AbstractPlugin implements SubFormPluginInterface
{

    /**
     * @var string
     */
    protected $countryIso2;

    /**
     * @var \Generated\Shared\Transfer\QuoteTransfer
     */
    protected $quoteTransfer;

    /**
     * @param string $countryIso2
     * @param \Generated\Shared\Transfer\QuoteTransfer $quoteTransfer
     */
    public function __construct($countryIso2, $quoteTransfer)
    {
        $this->countryIso2 = $countryIso2;
        $this->quoteTransfer = $quoteTransfer;
    }

    /**
     * @return \SprykerEco\Yves\Klarna\Form\InstallmentSubForm
     */
    public function createSubForm()
    {
        return $this->getFactory()->createInstallmentForm($this->countryIso2, $this->quoteTransfer, $this->createSubFormDataProvider());
    }

    /**
     * @author Daniel Bohnhardt <daniel.bohnhardt@twt.de>
     *
     * @return \SprykerEco\Yves\Klarna\Form\DataProvider\InstallmentDataProvider
     */
    public function createSubFormDataProvider()
    {
        return $this->getFactory()->createInstallmentDataProvider();
    }

}
