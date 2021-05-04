<?php

/**
 * MIT License
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Functional\SprykerEco\Zed\Klarna\Business\Api\Mock;

/**
 * Class KlarnaReserveMock
 *
 * @package Functional\SprykerEco\Zed\Klarna\Business\Api\Mock
 *
 * @author Daniel Bohnhardt <daniel.bohnhardt@twt.de>
 */
class KlarnaReserveMock extends KlarnaApiMockAbstract
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
    public function setException($exception)
    {
        $this->exception = $exception;
    }

    /**
     * @author Daniel Bohnhardt <daniel.bohnhardt@twt.de>
     *
     * @throws \Exception
     *
     * @return array
     */
    public function reserveAmount()
    {
        if ($this->exception) {
            throw $this->exception;
        }

        return ['testRefNo', 'testStatus'];
    }

}
