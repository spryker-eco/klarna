<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Client\Klarna;

use Spryker\Client\Kernel\AbstractFactory;
use Spryker\Client\Klarna\Session\KlarnaSession;
use Spryker\Client\Klarna\Zed\KlarnaStub;

/**
 * Class KlarnaFactory
 *
 * @package Spryker\Client\Klarna
 *
 * @author Daniel Bohnhardt <daniel.bohnhardt@twt.de>
 */
class KlarnaFactory extends AbstractFactory
{

    /**
     * @return \Spryker\Client\Klarna\Zed\KlarnaStub
     */
    public function createKlarnaStub()
    {
        return new KlarnaStub($this->createZedRequestClient());
    }

    /**
     * @return \Spryker\Client\Klarna\Session\KlarnaSession
     * @author Daniel Bohnhardt <daniel.bohnhardt@twt.de>
     */
    public function createKlarnaSession()
    {
        return new KlarnaSession($this->createSessionClient());
    }

}
