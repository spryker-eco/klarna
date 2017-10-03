<?php

/**
 * MIT License
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace SprykerEco\Zed\Klarna\Communication\Controller;

use Generated\Shared\Transfer\KlarnaCheckoutServiceRequestTransfer;
use Generated\Shared\Transfer\KlarnaCheckoutTransfer;
use Generated\Shared\Transfer\KlarnaGetAddressesRequestTransfer;
use Generated\Shared\Transfer\QuoteTransfer;
use Spryker\Zed\Kernel\Communication\Controller\AbstractGatewayController;

/**
 * Class GatewayController
 *
 * @package SprykerEco\Zed\Klarna\Communication\Controller
 *
 * @author Daniel Bohnhardt <daniel.bohnhardt@twt.de>
 * @method \SprykerEco\Zed\Klarna\Business\KlarnaFacade getFacade()
 */
class GatewayController extends AbstractGatewayController
{

    /**
     * @author Daniel Bohnhardt <daniel.bohnhardt@twt.de>
     *
     * @param \Generated\Shared\Transfer\QuoteTransfer $quoteTransfer
     *
     * @return \Generated\Shared\Transfer\KlarnaReserveAmountResponseTransfer
     */
    public function updatePaymentAction(QuoteTransfer $quoteTransfer)
    {
        return $this->getFacade()->updatePayment($quoteTransfer);
    }

    /**
     * @author Daniel Bohnhardt <daniel.bohnhardt@twt.de>
     *
     * @param \Generated\Shared\Transfer\QuoteTransfer $quoteTransfer
     *
     * @return \Generated\Shared\Transfer\KlarnaInstallmentResponseTransfer
     */
    public function installmentsAction(QuoteTransfer $quoteTransfer)
    {
        return $this->getFacade()->getInstallments($quoteTransfer);
    }

    /**
     * @author Daniel Bohnhardt <daniel.bohnhardt@twt.de>
     *
     * @param \Generated\Shared\Transfer\QuoteTransfer $quoteTransfer
     *
     * @return \Generated\Shared\Transfer\KlarnaCheckoutTransfer
     */
    public function klarnaCheckoutAction(QuoteTransfer $quoteTransfer)
    {
        return $this->getFacade()->getKlarnaCheckoutHtml($quoteTransfer);
    }

    /**
     * @author Daniel Bohnhardt <daniel.bohnhardt@twt.de>
     *
     * @param \Generated\Shared\Transfer\KlarnaCheckoutTransfer $klarnaCheckoutTransfer
     *
     * @return \Generated\Shared\Transfer\KlarnaCheckoutTransfer
     */
    public function klarnaSuccessAction(KlarnaCheckoutTransfer $klarnaCheckoutTransfer)
    {
        return $this->getFacade()->getKlarnaSuccessHtml($klarnaCheckoutTransfer);
    }

    /**
     * @author Daniel Bohnhardt <daniel.bohnhardt@twt.de>
     *
     * @param \Generated\Shared\Transfer\KlarnaCheckoutTransfer $klarnaCheckoutTransfer
     *
     * @return bool
     */
    public function createCheckoutOrderAction(KlarnaCheckoutTransfer $klarnaCheckoutTransfer)
    {
        return $this->getFacade()->createCheckoutOrder($klarnaCheckoutTransfer);
    }

    /**
     * @param \Generated\Shared\Transfer\KlarnaCheckoutServiceRequestTransfer $klarnaCheckoutServiceRequestTransfer
     *
     * @return \Generated\Shared\Transfer\KlarnaCheckoutServiceResponseTransfer
     */
    public function checkoutServiceAction(KlarnaCheckoutServiceRequestTransfer $klarnaCheckoutServiceRequestTransfer)
    {
        return $this->getFacade()->checkoutService($klarnaCheckoutServiceRequestTransfer);
    }

    /**
     * @param \Generated\Shared\Transfer\KlarnaGetAddressesRequestTransfer $klarnaGetAddressesRequestTransfer
     *
     * @return \Generated\Shared\Transfer\KlarnaGetAddressesResponseTransfer
     */
    public function getAddressesAction(KlarnaGetAddressesRequestTransfer $klarnaGetAddressesRequestTransfer)
    {
        return $this->getFacade()->getAddresses($klarnaGetAddressesRequestTransfer);
    }

}
