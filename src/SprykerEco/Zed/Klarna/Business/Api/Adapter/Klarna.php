<?php

/**
 * MIT License
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace SprykerEco\Zed\Klarna\Business\Api\Adapter;

use Klarna as KlarnaLib;
use SprykerEco\Shared\Klarna\KlarnaConfig;

class Klarna extends KlarnaLib
{
    /**
     * Updates version string. There is no other way to change it accordingly.
     */
    public function __construct()
    {
        parent::__construct();
        $this->VERSION = $this->VERSION . ':' . KlarnaConfig::KLARNA_BUNDLE_VERSION;
    }
}
