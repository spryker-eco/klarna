<?php
/**
 * MIT License
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */
namespace SprykerEco\Shared\Klarna\Library\Currency\Config;

use Spryker\Shared\Library\Currency\CurrencyInterface;

class NOK implements CurrencyInterface
{
    /**
     * @return string
     */
    public function getIsoCode()
    {
        return 'NOK';
    }

    /**
     * @return string
     */
    public function getSymbol()
    {
        return 'NOK';
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
