<?php

/**
 * MIT License
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace SprykerEcoTest\Zed\Klarna\Business\Api\Mock;

use Exception;

/**
 * Class KlarnaCaptureMock
 *
 * @package SprykerEcoTest\Zed\Klarna\Business\Api\Mock
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
     */
    public function setException(Exception $exception)
    {
        $this->exception = $exception;
    }

    /**
     * @throws \Exception
     *
     * @return array
     */
    public function activate()
    {
        if ($this->exception) {
            throw $this->exception;
        }

        return [
            'riskStatus',
            'invoiceNumber',
        ];
    }

    /**
     * @return void
     */
    public function setActivateInfo()
    {
    }
}
