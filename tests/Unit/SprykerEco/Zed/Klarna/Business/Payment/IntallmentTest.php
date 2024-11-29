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
use SprykerEco\Zed\Klarna\Business\Api\Handler\KlarnaApi;
use SprykerEco\Zed\Klarna\Business\Request\InstallmentRequest;
use SprykerEco\Zed\Klarna\Dependency\Facade\KlarnaToMoneyInterface;

/**
 * Class IntallmentTest
 *
 * @author   Daniel Bohnhardt <daniel.bohnhardt@twt.de>
 */
class IntallmentTest extends Test
{

    /**
     * @author Daniel Bohnhardt <daniel.bohnhardt@twt.de>
     *
     * @return void
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
     * @author Daniel Bohnhardt <daniel.bohnhardt@twt.de>
     *
     * @return \SprykerEco\Zed\Klarna\Business\Request\InstallmentRequest
     */
    protected function getInstallmentObject()
    {
        $klarnaApiMock = $this->getMockBuilder(KlarnaApi::class)
            ->disableOriginalConstructor()
            ->setMethods(['getPclasses'])
            ->getMock();

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
            $klarnaPclassObjectFail,
        ];

        $klarnaApiMock
            ->expects($this->once())
            ->method('getPclasses')
            ->willReturn($return);

        return new InstallmentRequest(
            $klarnaApiMock,
            new \SprykerEco\Zed\Klarna\Business\Request\Mapper\PClassRequestTransferMapper(),
            new \SprykerEco\Zed\Klarna\Business\Response\Mapper\InstallmentTransferMapper(
                $this->createMoneyFacade()
            )
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

}
