<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Functional\SprykerEco\Zed\Klarna\Business\Api\Mock;

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
     * @return string
     * @throws \Exception
     * @author Daniel Bohnhardt <daniel.bohnhardt@twt.de>
     */
    public function creditInvoice()
    {
        if ($this->exception) {
            throw $this->exception;
        }

        return 'invoiceNumber';
    }

    /**
     * @return string
     * @throws \Exception
     * @author Daniel Bohnhardt <daniel.bohnhardt@twt.de>
     */
    public function creditPart()
    {
        if ($this->exception) {
            throw $this->exception;
        }

        return 'invoicePartNumber';
    }

}
