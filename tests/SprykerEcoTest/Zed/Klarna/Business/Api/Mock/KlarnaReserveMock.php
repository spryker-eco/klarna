<?php

/**
 * MIT License
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace SprykerEcoTest\Zed\Klarna\Business\Api\Mock;

/**
 * Class KlarnaReserveMock
 *
 * @package SprykerEcoTest\Zed\Klarna\Business\Api\Mock
 */
class KlarnaReserveMock extends KlarnaApiMockAbstract
{
    /**
     * @var \Exception
     */
    protected $exception;

    /**
     * @param \Exception $exception
     *
     * @return void
     */
    public function setException($exception)
    {
        $this->exception = $exception;
    }

    /**
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
