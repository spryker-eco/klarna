<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
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
