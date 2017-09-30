<?php
/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */
namespace SprykerEco\Zed\Klarna\Communication\Plugin\Checkout;

use Generated\Shared\Transfer\CheckoutErrorTransfer;
use Generated\Shared\Transfer\CheckoutResponseTransfer;
use Generated\Shared\Transfer\QuoteTransfer;
use SprykerEco\Shared\Klarna\KlarnaConstants;
use Spryker\Zed\Kernel\Communication\AbstractPlugin as BaseAbstractPlugin;
use Spryker\Zed\Payment\Dependency\Plugin\Checkout\CheckoutPreCheckPluginInterface;

/**
 * Class KlarnaPreCheckPlugin
 *
 * @package SprykerEco\Zed\Klarna\Communication\Plugin\Checkout
 * @method \SprykerEco\Zed\Klarna\Business\KlarnaFacade getFacade()
 *
 * @author Daniel Bohnhardt <daniel.bohnhardt@twt.de>
 */
class KlarnaPreCheckPlugin extends BaseAbstractPlugin implements CheckoutPreCheckPluginInterface
{

    /**
     * @param \Generated\Shared\Transfer\QuoteTransfer $quoteTransfer
     * @param \Generated\Shared\Transfer\CheckoutResponseTransfer $checkoutResponseTransfer
     *
     * @return \Generated\Shared\Transfer\CheckoutResponseTransfer
     */
    public function execute(
        QuoteTransfer $quoteTransfer,
        CheckoutResponseTransfer $checkoutResponseTransfer
    ) {
        return $this->checkCondition($quoteTransfer, $checkoutResponseTransfer);
    }

    /**
     * @param \Generated\Shared\Transfer\QuoteTransfer $quoteTransfer
     * @param \Generated\Shared\Transfer\CheckoutResponseTransfer $checkoutResponseTransfer
     *
     * @return \Generated\Shared\Transfer\CheckoutResponseTransfer
     */
    public function checkCondition(QuoteTransfer $quoteTransfer, CheckoutResponseTransfer $checkoutResponseTransfer)
    {
        $klarnaReserveAmountResponseTransfer = $this->getFacade()->reserveAmount($quoteTransfer);

        if ($klarnaReserveAmountResponseTransfer->getReservationNo()) {
            $klarnaPaymentTransfer = $quoteTransfer->getPayment()->getKlarna();

            $klarnaPaymentTransfer
                ->setPreCheckId($klarnaReserveAmountResponseTransfer->getReservationNo())
                ->setPendingStatus(
                    (int)($klarnaReserveAmountResponseTransfer->getStatus() !== KlarnaConstants::ORDER_PENDING_ACCEPTED)
                )
                ->setStatus($this->mapStatus($klarnaReserveAmountResponseTransfer->getStatus()));

        }

        if ($klarnaReserveAmountResponseTransfer->getError()) {
            $quoteTransfer->setPayment(null);
            $error = new CheckoutErrorTransfer();
            $error->setMessage($klarnaReserveAmountResponseTransfer->getError());

            $checkoutResponseTransfer->addError($error);
        }

        return $checkoutResponseTransfer;
    }

    /**
     * @param int $klarnaStatus
     * @return int
     */
    protected function mapStatus($klarnaStatus)
    {
        if ($klarnaStatus === \KlarnaFlags::ACCEPTED) {
            $status = KlarnaConstants::ORDER_PENDING_ACCEPTED;
        } elseif ($klarnaStatus === \KlarnaFlags::DENIED) {
            $status = KlarnaConstants::ORDER_PENDING_DENIED;
        } else {
            $status = KlarnaConstants::ORDER_PENDING;
        }

        return $status;
    }

}
