<?php
/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */
namespace Spryker\Shared\Klarna\Library\Currency\Config;

use Spryker\Shared\Library\Currency\CurrencyInterface;

class DKK implements CurrencyInterface
{

    /**
     * @return string
     */
    public function getIsoCode()
    {
        return 'DKK';
    }

    /**
     * @return string
     */
    public function getSymbol()
    {
        return 'DKK';
    }

    /**
     * @return string
     */
    public function getThousandsSeparator()
    {
        return '.';
    }

    /**
     * @return string
     */
    public function getDecimalSeparator()
    {
        return ',';
    }

    /**
     * @return string
     */
    public function getDecimalDigits()
    {
        return 2;
    }

    /**
     * @return string
     */
    public function getFormatPattern()
    {
        return '{v} {s}';
    }

}
