<?php

/**
 * MIT License
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace SprykerEcoTest\Zed\Klarna\Business\Api\Mock;

use Exception;

/**
 * Class KlarnaRefundMock
 *
 * @package SprykerEcoTest\Zed\Klarna\Business\Api\Mock
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
     */
    public function setException(Exception $exception)
    {
        $this->exception = $exception;
    }

    /**
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
