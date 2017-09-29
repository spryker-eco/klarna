<?php
/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */
namespace Spryker\Zed\Klarna\Business\Api\Handler;

use Spryker\Shared\Config\Config;
use Spryker\Shared\Klarna\KlarnaConstants;
use Spryker\Zed\Klarna\Business\Api\Handler\KlarnaAddressCreator\AustriaKlarnaAddressCreator;
use Spryker\Zed\Klarna\Business\Api\Handler\KlarnaAddressCreator\DefaultKlarnaAddressCreator;
use Spryker\Zed\Klarna\Business\Api\Handler\KlarnaAddressCreator\GermanyKlarnaAddressCreator;
use Spryker\Zed\Klarna\Business\Api\Handler\KlarnaAddressCreator\NetherlandKlarnaAddressCreator;

class KlarnaAddressFactory
{

    const DEFAULT_COUNTRY = 'default';

    /**
     * @var \Pyz\Yves\Klarna\Plugin\SubFormsCreator\SubFormsCreatorInterface[]
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
     * @return \Spryker\Zed\Klarna\Business\Api\Handler\KlarnaAddressCreator\KlarnaAddressCreatorInterface
     */
    public function createKlarnaAddressCreator($countryIso2Code)
    {
        if (isset($this->klarnaAddressCreators[$countryIso2Code])) {
            $klarnaAddressCreator = $this->klarnaAddressCreators[$countryIso2Code]();
        } else {
            $klarnaAddressCreator = $this->klarnaAddressCreators[self::DEFAULT_COUNTRY]();
        }

        return $klarnaAddressCreator;
    }

}
