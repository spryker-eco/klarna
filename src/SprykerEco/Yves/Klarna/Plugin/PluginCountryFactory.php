<?php

/**
 * MIT License
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace SprykerEco\Yves\Klarna\Plugin;

use Spryker\Shared\Config\Config;
use Spryker\Yves\Kernel\AbstractPlugin;
use SprykerEco\Shared\Klarna\KlarnaConstants;
use SprykerEco\Yves\Klarna\Plugin\SubFormsCreator\AustriaSubFormsCreator;
use SprykerEco\Yves\Klarna\Plugin\SubFormsCreator\DefaultSubFormsCreator;
use SprykerEco\Yves\Klarna\Plugin\SubFormsCreator\DenmarkSubFormsCreator;
use SprykerEco\Yves\Klarna\Plugin\SubFormsCreator\FinlandSubFormsCreator;
use SprykerEco\Yves\Klarna\Plugin\SubFormsCreator\GermanySubFormsCreator;
use SprykerEco\Yves\Klarna\Plugin\SubFormsCreator\NetherlandSubFormsCreator;
use SprykerEco\Yves\Klarna\Plugin\SubFormsCreator\NorwaySubFormsCreator;
use SprykerEco\Yves\Klarna\Plugin\SubFormsCreator\SwedenSubFormsCreator;

/**
 * @package SprykerEco\Yves\Klarna\Plugin
 * @method \SprykerEco\Yves\Klarna\KlarnaFactory getFactory()
 */
class PluginCountryFactory extends AbstractPlugin
{
    const DEFAULT_COUNTRY = 'default';

    /**
     * @var \SprykerEco\Yves\Klarna\Plugin\SubFormsCreator\SubFormsCreatorInterface[]
     */
    protected $subFormsCreators = [];

    public function __construct()
    {
        $this->subFormsCreators = [
            Config::getInstance()->get(KlarnaConstants::COUNTRY_AUSTRIA) => function () {
                return new AustriaSubFormsCreator();
            },
            Config::getInstance()->get(KlarnaConstants::COUNTRY_NETHERLAND) => function () {
                return new NetherlandSubFormsCreator();
            },
            Config::getInstance()->get(KlarnaConstants::COUNTRY_FINLAND) => function () {
                return new FinlandSubFormsCreator();
            },
            Config::getInstance()->get(KlarnaConstants::COUNTRY_DENMARK) => function () {
                return new DenmarkSubFormsCreator();
            },
            // For next countries we use checkoutService. But temporary use concrete subFormCreator
            Config::getInstance()->get(KlarnaConstants::COUNTRY_GERMANY) => function () {
                 return new GermanySubFormsCreator();
            },
            Config::getInstance()->get(KlarnaConstants::COUNTRY_NORWAY) => function () {
                 return new NorwaySubFormsCreator();
            },
            Config::getInstance()->get(KlarnaConstants::COUNTRY_SWEDEN) => function () {
                 return new SwedenSubFormsCreator();
            },
            self::DEFAULT_COUNTRY => function () {
                return new DefaultSubFormsCreator($this->getFactory()->getKlarnaClient());
            },
        ];
    }

    /**
     * @param string $countryIso2Code
     *
     * @return \SprykerEco\Yves\Klarna\Plugin\SubFormsCreator\SubFormsCreatorInterface
     */
    public function getSubFormsCreator($countryIso2Code)
    {
        if (isset($this->subFormsCreators[$countryIso2Code])) {
            $subFormsCreator = $this->subFormsCreators[$countryIso2Code]();
        } else {
            $subFormsCreator = $this->subFormsCreators[self::DEFAULT_COUNTRY]();
        }

        return $subFormsCreator;
    }
}
