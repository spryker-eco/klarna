<?php
/**
 * MIT License
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */
namespace SprykerEco\Zed\Klarna\Business\Api\Handler;

use Spryker\Shared\Config\Config;
use SprykerEco\Shared\Klarna\KlarnaConstants;
use SprykerEco\Zed\Klarna\Business\Api\Handler\KlarnaAddressCreator\AustriaKlarnaAddressCreator;
use SprykerEco\Zed\Klarna\Business\Api\Handler\KlarnaAddressCreator\DefaultKlarnaAddressCreator;
use SprykerEco\Zed\Klarna\Business\Api\Handler\KlarnaAddressCreator\GermanyKlarnaAddressCreator;
use SprykerEco\Zed\Klarna\Business\Api\Handler\KlarnaAddressCreator\NetherlandKlarnaAddressCreator;

class KlarnaAddressFactory
{
    const DEFAULT_COUNTRY = 'default';

    /**
     * @var \SprykerEco\Yves\Klarna\Plugin\SubFormsCreator\SubFormsCreatorInterface[]
     */
    protected $klarnaAddressCreators = [];

    public function __construct()
    {
        $this->klarnaAddressCreators = [
            Config::getInstance()->get(KlarnaConstants::COUNTRY_AUSTRIA) => function () {
                return new AustriaKlarnaAddressCreator();
            },
            Config::getInstance()->get(KlarnaConstants::COUNTRY_GERMANY) => function () {
                return new GermanyKlarnaAddressCreator();
            },
            Config::getInstance()->get(KlarnaConstants::COUNTRY_NETHERLAND) => function () {
                return new NetherlandKlarnaAddressCreator();
            },
            self::DEFAULT_COUNTRY => function () {
                return new DefaultKlarnaAddressCreator();
            },
        ];
    }

    /**
     * @param string $countryIso2Code
     *
     * @return \SprykerEco\Zed\Klarna\Business\Api\Handler\KlarnaAddressCreator\KlarnaAddressCreatorInterface
     */
    public function getKlarnaAddressCreator($countryIso2Code)
    {
        if (isset($this->klarnaAddressCreators[$countryIso2Code])) {
            return $this->klarnaAddressCreators[$countryIso2Code]();
        }
        return $this->klarnaAddressCreators[self::DEFAULT_COUNTRY]();
    }
}
