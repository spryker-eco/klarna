<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace SprykerEco\Zed\Klarna\Business\Api\Adapter;

use Klarna as KlarnaLib;
use SprykerEco\Shared\Klarna\KlarnaConstants;

class Klarna extends KlarnaLib
{

    /**
     * Updates version string. There is no other way to change it accordingly.
     */
    public function __construct()
    {
        parent::__construct();
        $this->VERSION = $this->VERSION . ':' . KlarnaConstants::KLARNA_BUNDLE_VERSION;
    }

}
