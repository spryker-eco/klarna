<?php

/**
 * MIT License
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace SprykerEcoTest\Zed\Klarna\Business\Payment;

use Codeception\TestCase\Test;
use Generated\Shared\Transfer\QuoteTransfer;
use SprykerEco\Zed\Klarna\Business\Api\Handler\KlarnaApi;
use SprykerEco\Zed\Klarna\Business\Request\ReserveAmount;

class ReserveAmountTest extends Test
{
    /**
     * @return void
     */
    public function testReserveAmount()
    {
        $reserveAmount = $this->getReserveAmountObject();
        $return = $reserveAmount->createReserveAmountTransfer(new QuoteTransfer());
        $this->assertInstanceOf('Generated\Shared\Transfer\KlarnaReserveAmountResponseTransfer', $return);

        $this->assertSame('testStatus', $return->getStatus());
        $this->assertSame('testRefNo', $return->getReservationNo());
        $this->assertSame('testError', $return->getError());
    }

    /**
     * @return void
     */
    public function testUpdateReservation()
    {
        $reserveAmount = $this->getReserveAmountObject();
        $return = $reserveAmount->createUpdateReserveAmountTransfer(new QuoteTransfer());
        $this->assertInstanceOf('Generated\Shared\Transfer\KlarnaReserveAmountResponseTransfer', $return);

        $this->assertSame(1, $return->getStatus());
    }

    /**
     * @return void
     */
    public function testUpdateReservationFailed()
    {
        $reserveAmount = $this->getReserveAmountObject(true);
        $return = $reserveAmount->createUpdateReserveAmountTransfer(new QuoteTransfer());
        $this->assertInstanceOf('Generated\Shared\Transfer\KlarnaReserveAmountResponseTransfer', $return);

        $this->assertSame(0, $return->getStatus());
        $this->assertSame('testError', $return->getError());
    }

    /**
     * @param bool $returnUpdateError
     *
     * @return \SprykerEco\Zed\Klarna\Business\Request\ReserveAmount
     */
    protected function getReserveAmountObject($returnUpdateError = false)
    {
        $klarnaApiMock = $this->getMockBuilder(KlarnaApi::class)
            ->disableOriginalConstructor()
            ->setMethods(['reserveAmount', 'update'])
            ->getMock();
        $klarnaApiMock
            ->expects($this->any())
            ->method('reserveAmount')
            ->willReturn(
                [
                    0 => 'testRefNo',
                    1 => 'testStatus',
                    2 => 'testError',
                ]
            );

        if ($returnUpdateError) {
            $klarnaApiMock->expects($this->any())->method('update')->willReturn('testError');
        } else {
            $klarnaApiMock->expects($this->any())->method('update')->willReturn('ok');
        }

        return new ReserveAmount($klarnaApiMock);
    }
}
