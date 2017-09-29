<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Functional\Spryker\Zed\Klarna\Business\Api\Mock;

/**
 * Class KlarnaCaptureMock
 *
 * @package Functional\Spryker\Zed\Klarna\Business\Api\Mock
 *
 * @author Daniel Bohnhardt <daniel.bohnhardt@twt.de>
 */
class KlarnaCaptureMock extends KlarnaApiMockAbstract
{

    /**
     * @var \Exception
     */
    protected $exception;

    /**
     * @param \Exception $exception
     *
     * @return void
     * @author Daniel Bohnhardt <daniel.bohnhardt@twt.de>
     */
    public function setException(\Exception $exception)
    {
        $this->exception = $exception;
    }

    /**
     * @return array
     * @throws \Exception
     * @author Daniel Bohnhardt <daniel.bohnhardt@twt.de>
     */
    public function activate()
    {
        if ($this->exception) {
            throw $this->exception;
        }

        return [
            'riskStatus',
            'invoiceNumber'
        ];
    }

    /**
     * @return void
     * @author Daniel Bohnhardt <daniel.bohnhardt@twt.de>
     */
    public function setActivateInfo()
    {
    }

}
