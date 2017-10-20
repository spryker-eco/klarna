<?php

/**
 * MIT License
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace SprykerEco\Zed\Klarna\Dependency\Facade;

/**
 * Interface KlarnaToMoneyInterface
 *
 * @package SprykerEco\Zed\Klarna\Dependency\Facade
 *
 */
interface KlarnaToMoneyInterface
{
    /**
     * Specification
     * - Converts an integer value into decimal value
     *
     * @api
     *
     * @param int $value
     *
     * @return float
     */
    public function convertIntegerToDecimal($value);

    /**
     * Specification
     * - Converts a decimal value into integer value
     *
     * @api
     *
     * @param float $value
     *
     * @return int
     */
    public function convertDecimalToInteger($value);
}
