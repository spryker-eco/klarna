<?php
/**
 * MIT License
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */
namespace SprykerEco\Shared\Klarna\Library\Currency\Config;

use Spryker\Shared\Library\Currency\CurrencyInterface;

class SEK implements CurrencyInterface
{
    /**
     * @return string
     */
    public function getIsoCode()
    {
        return 'SEK';
    }

    /**
     * @return string
     */
    public function getSymbol()
    {
        return 'SEK';
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
