<?php
/*
 * This file is part of the TWT eCommerce platform package.
 *
 * (c) TWT Interactive GmbH <info@twt.de>
 *
 * For the full copyright, license and further information contact TWT.
*/

use Codeception\TestCase\Test;
use Generated\Shared\Transfer\QuoteTransfer;
use SprykerEco\Zed\Klarna\Business\Request\ReserveAmount;

/**
 * Class ReserveAmountTest
 *
 * @author   Daniel Bohnhardt <daniel.bohnhardt@twt.de>
 */
class ReserveAmountTest extends Test
{

    /**
     * @author Daniel Bohnhardt <daniel.bohnhardt@twt.de>
     *
     * @return void
     */
    public function testReserveAmount()
    {
        $reserveAmount = $this->getReserveAmountObject();
        $return = $reserveAmount->reserveAmount(new QuoteTransfer());
        $this->assertInstanceOf('Generated\Shared\Transfer\KlarnaReserveAmountResponseTransfer', $return);

        $this->assertSame('testStatus', $return->getStatus());
        $this->assertSame('testRefNo', $return->getReservationNo());
        $this->assertSame('testError', $return->getError());
    }

    /**
     * @author Daniel Bohnhardt <daniel.bohnhardt@twt.de>
     *
     * @return void
     */
    public function testUpdateReservation()
    {
        $reserveAmount = $this->getReserveAmountObject();
        $return = $reserveAmount->updateReservation(new QuoteTransfer());
        $this->assertInstanceOf('Generated\Shared\Transfer\KlarnaReserveAmountResponseTransfer', $return);

        $this->assertSame(1, $return->getStatus());
    }

    /**
     * @author Daniel Bohnhardt <daniel.bohnhardt@twt.de>
     *
     * @return void
     */
    public function testUpdateReservationFailed()
    {
        $reserveAmount = $this->getReserveAmountObject(true);
        $return = $reserveAmount->updateReservation(new QuoteTransfer());
        $this->assertInstanceOf('Generated\Shared\Transfer\KlarnaReserveAmountResponseTransfer', $return);

        $this->assertSame(0, $return->getStatus());
        $this->assertSame('testError', $return->getError());
    }

    /**
     * @author Daniel Bohnhardt <daniel.bohnhardt@twt.de>
     *
     * @param bool $returnUpdateError
     *
     * @return \SprykerEco\Zed\Klarna\Business\Request\ReserveAmount
     */
    protected function getReserveAmountObject($returnUpdateError = false)
    {
        $klarnaApiMock = $this->getMock(
            'SprykerEco\Zed\Klarna\Business\Api\Handler\KlarnaApi',
            [
                'reserveAmount', 'update',
            ],
            [],
            '',
            false
        );
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

        $reserveAmount = new ReserveAmount($klarnaApiMock);

        return $reserveAmount;
    }

}
