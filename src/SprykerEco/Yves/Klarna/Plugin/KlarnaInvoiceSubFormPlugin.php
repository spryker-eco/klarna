<?php

/**
 * MIT License
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace SprykerEco\Yves\Klarna\Plugin;

use Spryker\Yves\Kernel\AbstractPlugin;
use Spryker\Yves\StepEngine\Dependency\Plugin\Form\SubFormPluginInterface;

/**
 * Class KlarnaInvoiceSubFormPlugin
 *
 * @package SprykerEco\Yves\Klarna\Plugin
 * @method \SprykerEco\Yves\Klarna\KlarnaFactory getFactory()
 *
 * @author Daniel Bohnhardt <daniel.bohnhardt@twt.de>
 */
class KlarnaInvoiceSubFormPlugin extends AbstractPlugin implements SubFormPluginInterface
{

    /**
     * @var string
     */
    protected $countryIso2;

    /**
     * @param string $countryIso2
     */
    public function __construct($countryIso2)
    {
        $this->countryIso2 = $countryIso2;
    }

    /**
     * @return \Spryker\Yves\StepEngine\Dependency\Form\SubFormInterface
     */
    public function createSubForm()
    {
        return $this->getFactory()->createInvoiceForm($this->countryIso2);
    }

    /**
     * @return \Spryker\Yves\StepEngine\Dependency\Form\StepEngineFormDataProviderInterface
     */
    public function createSubFormDataProvider()
    {
        return $this->getFactory()->createInvoiceDataProvider();
    }

}
