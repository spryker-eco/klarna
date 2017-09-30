<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace SprykerEco\Zed\Klarna\Business\Response\Mapper;

use Generated\Shared\Transfer\KlarnaInstallmentResponseTransfer;
use Generated\Shared\Transfer\KlarnaPClassTransfer;
use Generated\Shared\Transfer\QuoteTransfer;
use Spryker\Shared\Library\Currency\CurrencyManager;

class InstallmentTransferMapper
{

    /**
     * @var \Spryker\Shared\Library\Currency\CurrencyManager
     */
    protected $currencyManager;

    /**
     * @param \Generated\Shared\Transfer\QuoteTransfer $quoteTransfer
     * @param \KlarnaPClass[] $pClasses
     * @param \Spryker\Shared\Library\Currency\CurrencyManager
     *
     * @return \Generated\Shared\Transfer\KlarnaInstallmentResponseTransfer
     */
    public function map(
        QuoteTransfer $quoteTransfer,
        $pClasses,
        CurrencyManager $currencyManager
    ) {
        $this->currencyManager = $currencyManager;

        $orderAmount = $quoteTransfer->getTotals()->getGrandTotal();
        $payments = new \ArrayObject();
        foreach ($pClasses as $key => $pClass) {
            if ($orderAmount < $pClass->getMinAmount()) {
                unset($pClasses[$key]);
                continue;
            }

            $payments->append($this->convertToInstallmentTransfer($pClass, $orderAmount));
        }

        $klarnaInstallmentResponseTransfer = new KlarnaInstallmentResponseTransfer();
        $klarnaInstallmentResponseTransfer->setPayments($payments);

        return $klarnaInstallmentResponseTransfer;
    }

    /**
     * @param \KlarnaPClass $pClass
     * @param int $orderAmount
     *
     * @return \Generated\Shared\Transfer\KlarnaPClassTransfer
     */
    protected function convertToInstallmentTransfer(\KlarnaPClass $pClass, $orderAmount)
    {
        $amount = $this->currencyManager->convertCentToDecimal($orderAmount);
        $monthlyCosts = \KlarnaCalc::calc_monthly_cost($amount, $pClass, \KlarnaFlags::CHECKOUT_PAGE);
        $apr = \KlarnaCalc::calc_apr($amount, $pClass, \KlarnaFlags::CHECKOUT_PAGE);
        $totalCreditPurchaseCost = \KlarnaCalc::total_credit_purchase_cost($amount, $pClass, \KlarnaFlags::CHECKOUT_PAGE);

        $transfer = new KlarnaPClassTransfer();
        $transfer
            ->setType($pClass->getType())
            ->setExpire($pClass->getExpire())
            ->setId($pClass->getId())
            ->setMinAmount($pClass->getMinAmount())
            ->setMonth($pClass->getMonths())
            ->setName($pClass->getDescription())
            ->setStartFee($pClass->getStartFee())
            ->setInvoiceFee($pClass->getInvoiceFee())
            ->setInterestRate($pClass->getInterestRate())
            ->setMonthlyCosts($monthlyCosts)
            ->setApr($apr)
            ->setTotalCreditPurchaseCost($totalCreditPurchaseCost)
        ;

        return $transfer;
    }

}
