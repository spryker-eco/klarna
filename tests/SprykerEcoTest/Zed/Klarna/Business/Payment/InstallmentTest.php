<?php

/**
 * MIT License
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace SprykerEcoTest\Zed\Klarna\Business\Payment;

use Codeception\TestCase\Test;
use Generated\Shared\Transfer\CurrencyTransfer;
use Generated\Shared\Transfer\KlarnaInstallmentResponseTransfer;
use Generated\Shared\Transfer\KlarnaPaymentTransfer;
use Generated\Shared\Transfer\KlarnaPClassTransfer;
use Generated\Shared\Transfer\PaymentTransfer;
use Generated\Shared\Transfer\QuoteTransfer;
use Generated\Shared\Transfer\TotalsTransfer;
use KlarnaPClass;
use SprykerEco\Zed\Klarna\Business\Api\Handler\KlarnaApi;
use SprykerEco\Zed\Klarna\Business\Request\InstallmentRequest;
use SprykerEco\Zed\Klarna\Business\Request\Mapper\PClassRequestTransferMapper;
use SprykerEco\Zed\Klarna\Business\Response\Mapper\InstallmentTransferMapper;
use SprykerEco\Zed\Klarna\Dependency\Facade\KlarnaToMoneyInterface;

class InstallmentTest extends Test
{
    /**
     * @return void
     */
    public function testGetInstallments()
    {
        $quoteTransfer = new QuoteTransfer();

        $totalTransfer = new TotalsTransfer();
        $totalTransfer->setGrandTotal(20);
        $quoteTransfer->setTotals($totalTransfer);
        $quoteTransfer->setCurrency($this->getCurrency());

        $klarnaPaymentTransfer = new KlarnaPaymentTransfer();
        $paymentTransfer = new PaymentTransfer();
        $paymentTransfer->setKlarna($klarnaPaymentTransfer);
        $quoteTransfer->setPayment($paymentTransfer);

        $installment = $this->getInstallmentObject();
        $return = $installment->getInstallments($quoteTransfer);

        $this->assertInstanceOf(KlarnaInstallmentResponseTransfer::class, $return);

        $payments = $return->getPayments();
        $this->assertCount(1, $payments);

        $payment = current($payments);
        $this->assertInstanceOf(KlarnaPClassTransfer::class, $payment);
        $this->assertSame(10.0, $payment->getMinAmount());
        $this->assertSame(1, $payment->getExpire());
        $this->assertSame(123, $payment->getId());
        $this->assertSame(5, $payment->getMonth());
        $this->assertSame(4.0, $payment->getStartFee());
        $this->assertSame(2.0, $payment->getInvoiceFee());
        $this->assertSame('testPclass', $payment->getName());
    }

    /**
     * @return \SprykerEco\Zed\Klarna\Business\Request\InstallmentRequest
     */
    protected function getInstallmentObject()
    {
        $klarnaApiMock = $this->getMockBuilder(KlarnaApi::class)
            ->disableOriginalConstructor()
            ->setMethods(['getPclasses'])
            ->getMock();

        $klarnaPclassObject = new KlarnaPClass();
        $klarnaPclassObject->setMinAmount(10);
        $klarnaPclassObject->setExpire(1);
        $klarnaPclassObject->setId(123);
        $klarnaPclassObject->setMonths(5);
        $klarnaPclassObject->setDescription('testPclass');
        $klarnaPclassObject->setStartFee(4);
        $klarnaPclassObject->setInvoiceFee(2);
        $klarnaPclassObject->setCountry(81);

        $klarnaPclassObjectFail = new KlarnaPClass();
        $klarnaPclassObjectFail->setMinAmount(100);

        $return = [
            $klarnaPclassObject,
            $klarnaPclassObjectFail,
        ];

        $klarnaApiMock
            ->expects($this->once())
            ->method('getPclasses')
            ->willReturn($return);

        return new InstallmentRequest(
            $klarnaApiMock,
            new PClassRequestTransferMapper(),
            new InstallmentTransferMapper($this->createMoneyFacade())
        );
    }

    /**
     * @return \SprykerEco\Zed\Klarna\Dependency\Facade\KlarnaToMoneyBridge
     */
    protected function createMoneyFacade()
    {
        $moneyFacade = $this->getMockBuilder(KlarnaToMoneyInterface::class)
            ->setMethods(['convertIntegerToDecimal', 'convertDecimalToInteger'])
            ->getMock();
        $moneyFacade->expects($this->once())
            ->method('convertIntegerToDecimal')
            ->willReturn(0.2);

        return $moneyFacade;
    }

    /**
     * @return \Generated\Shared\Transfer\CurrencyTransfer
     */
    private function getCurrency()
    {
        return (new CurrencyTransfer())
            ->setCode('EUR');
    }
}
