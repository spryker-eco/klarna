<?php
/*
 * This file is part of the TWT eCommerce platform package.
 *
 * (c) TWT Interactive GmbH <info@twt.de>
 *
 * For the full copyright, license and further information contact TWT.
*/

use Codeception\TestCase\Test;
use Generated\Shared\Transfer\KlarnaPaymentTransfer;
use Generated\Shared\Transfer\PaymentTransfer;
use Generated\Shared\Transfer\QuoteTransfer;
use Generated\Shared\Transfer\TotalsTransfer;
use Spryker\Zed\Klarna\Business\Request\InstallmentRequest;

/**
 * Class IntallmentTest
 *
 * @author   Daniel Bohnhardt <daniel.bohnhardt@twt.de>
 */
class IntallmentTest extends Test
{

    /**
     * @return void
     * @author Daniel Bohnhardt <daniel.bohnhardt@twt.de>
     */
    public function testGetInstallments()
    {
        $quoteTransfer = new QuoteTransfer();

        $totalTransfer = new TotalsTransfer();
        $totalTransfer->setGrandTotal(20);
        $quoteTransfer->setTotals($totalTransfer);

        $klarnaPaymentTransfer = new KlarnaPaymentTransfer();
        $paymentTransfer = new PaymentTransfer();
        $paymentTransfer->setKlarna($klarnaPaymentTransfer);
        $quoteTransfer->setPayment($paymentTransfer);

        $installment = $this->getInstallmentObject();
        $return = $installment->getInstallments($quoteTransfer);

        $this->assertInstanceOf('\Generated\Shared\Transfer\KlarnaInstallmentResponseTransfer', $return);

        $payments = $return->getPayments();
        $this->assertCount(1, $payments);

        /** @var \Generated\Shared\Transfer\KlarnaPClassTransfer $payment */
        $payment = current($payments);
        $this->assertInstanceOf('\Generated\Shared\Transfer\KlarnaPClassTransfer', $payment);
        $this->assertSame(10.0, $payment->getMinAmount());
        $this->assertSame(1, $payment->getExpire());
        $this->assertSame(123, $payment->getId());
        $this->assertSame(5, $payment->getMonth());
        $this->assertSame(4.0, $payment->getStartFee());
        $this->assertSame(2.0, $payment->getInvoiceFee());
        $this->assertSame('testPclass', $payment->getName());
    }

    /**
     * @return \Spryker\Zed\Klarna\Business\Request\InstallmentRequest
     * @author Daniel Bohnhardt <daniel.bohnhardt@twt.de>
     */
    protected function getInstallmentObject()
    {
        $klarnaApiMock = $this->getMock(
            'Spryker\Zed\Klarna\Business\Api\Handler\KlarnaApi',
            [
                'getPclasses'
            ],
            [],
            '',
            false
        );

        $klarnaPclassObject = new \KlarnaPClass();
        $klarnaPclassObject->setMinAmount(10);
        $klarnaPclassObject->setExpire(1);
        $klarnaPclassObject->setId(123);
        $klarnaPclassObject->setMonths(5);
        $klarnaPclassObject->setDescription('testPclass');
        $klarnaPclassObject->setStartFee(4);
        $klarnaPclassObject->setInvoiceFee(2);
        $klarnaPclassObject->setCountry(81);

        $klarnaPclassObjectFail = new \KlarnaPClass();
        $klarnaPclassObjectFail->setMinAmount(100);

        $return = [
            $klarnaPclassObject,
            $klarnaPclassObjectFail
        ];

        $klarnaApiMock
            ->expects($this->once())
            ->method('getPclasses')
            ->willReturn($return);

        return new InstallmentRequest(
            $klarnaApiMock,
            new \Spryker\Zed\Klarna\Business\Request\Mapper\PClassRequestTransferMapper(),
            new \Spryker\Zed\Klarna\Business\Response\Mapper\InstallmentTransferMapper()
        );
    }

}
