<?php

/**
 * MIT License
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace SprykerEco\Zed\Klarna\Dependency\Facade;

/**
 * @package SprykerEco\Zed\Klarna\Dependency\Facade
 */
interface KlarnaToLocaleInterface
{
    /**
     * @return string
     */
    public function getCurrentLocaleName();
}
