<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Functional\SprykerEco\Zed\Klarna\Business\Api\Mock;

use Exception;

/**
 * Class KlarnaRefundMock
 *
 * @package Functional\SprykerEco\Zed\Klarna\Business\Api\Mock
 *
 * @author Daniel Bohnhardt <daniel.bohnhardt@twt.de>
 */
class KlarnaRefundMock extends KlarnaApiMockAbstract
{

    /**
     * @var \Exception
     */
    protected $exception;

    /**
     * @author Daniel Bohnhardt <daniel.bohnhardt@twt.de>
     *
     * @param \Exception $exception
     *
     * @return void
     */
    public function setException(Exception $exception)
    {
        $this->exception = $exception;
    }

    /**
     * @author Daniel Bohnhardt <daniel.bohnhardt@twt.de>
     *
     * @throws \Exception
     *
     * @return string
     */
    public function creditInvoice()
    {
        if ($this->exception) {
            throw $this->exception;
        }

        return 'invoiceNumber';
    }

    /**
     * @author Daniel Bohnhardt <daniel.bohnhardt@twt.de>
     *
     * @throws \Exception
     *
     * @return string
     */
    public function creditPart()
    {
        if ($this->exception) {
            throw $this->exception;
        }

        return 'invoicePartNumber';
    }

}
