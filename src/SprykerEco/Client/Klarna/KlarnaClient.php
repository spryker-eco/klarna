<?php

/**
 * MIT License
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace SprykerEco\Client\Klarna;

use Generated\Shared\Transfer\KlarnaCheckoutServiceRequestTransfer;
use Generated\Shared\Transfer\KlarnaCheckoutTransfer;
use Generated\Shared\Transfer\KlarnaGetAddressesRequestTransfer;
use Generated\Shared\Transfer\KlarnaInstallmentResponseTransfer;
use Generated\Shared\Transfer\QuoteTransfer;
use Spryker\Client\Kernel\AbstractClient;

/**
 * Class KlarnaClient
 *
 * @package SprykerEco\Client\Klarna
 *
 * @author Daniel Bohnhardt <daniel.bohnhardt@twt.de>
 * @method \SprykerEco\Client\Klarna\KlarnaFactory getFactory()
 */
class KlarnaClient extends AbstractClient implements KlarnaClientInterface
{
    /**
     * @api
     *
     * @author Daniel Bohnhardt <daniel.bohnhardt@twt.de>
     *
     * @param \Generated\Shared\Transfer\QuoteTransfer $quoteTransfer
     *
     * @return \Generated\Shared\Transfer\KlarnaReserveAmountResponseTransfer
     */
    public function updatePayment(QuoteTransfer $quoteTransfer)
    {
        return $this->getFactory()->createKlarnaStub()->updatePayment($quoteTransfer);
    }

    /**
     * @api
     *
     * @author Daniel Bohnhardt <daniel.bohnhardt@twt.de>
     *
     * @param \Generated\Shared\Transfer\QuoteTransfer $quoteTransfer
     *
     * @return \Generated\Shared\Transfer\KlarnaInstallmentResponseTransfer
     */
    public function getInstallments(QuoteTransfer $quoteTransfer)
    {
        return $this->getFactory()->createKlarnaStub()->getInstallments($quoteTransfer);
    }

    /**
     * @api
     *
     * @author Daniel Bohnhardt <daniel.bohnhardt@twt.de>
     *
     * @return \SprykerEco\Client\Klarna\Session\KlarnaSession
     */
    public function getSession()
    {
        return $this->getFactory()->createKlarnaSession();
    }

    /**
     * @api
     *
     * @author Daniel Bohnhardt <daniel.bohnhardt@twt.de>
     *
     * @return bool
     */
    public function hasInstallmentsInSession()
    {
        return $this->getSession()->hasInstallments();
    }

    /**
     * @api
     *
     * @author Daniel Bohnhardt <daniel.bohnhardt@twt.de>
     *
     * @param \Generated\Shared\Transfer\KlarnaInstallmentResponseTransfer $installmentResponseTransfer
     *
     * @return \Generated\Shared\Transfer\KlarnaInstallmentResponseTransfer
     */
    public function storeInstallmentsInSession(KlarnaInstallmentResponseTransfer $installmentResponseTransfer)
    {
        $this->getSession()->setInstallments($installmentResponseTransfer);

        return $installmentResponseTransfer;
    }

    /**
     * @api
     *
     * @author Daniel Bohnhardt <daniel.bohnhardt@twt.de>
     *
     * @return bool
     */
    public function removeInstallmentsFromSession()
    {
        return $this->getSession()->removeInstallments();
    }

    /**
     * @api
     *
     * @author Daniel Bohnhardt <daniel.bohnhardt@twt.de>
     *
     * @return \Generated\Shared\Transfer\KlarnaInstallmentResponseTransfer
     */
    public function getInstallmentsFromSession()
    {
        return $this->getSession()->getInstallments();
    }

    /**
     * @api
     *
     * @author Daniel Bohnhardt <daniel.bohnhardt@twt.de>
     *
     * @param \Generated\Shared\Transfer\QuoteTransfer $quoteTransfer
     *
     * @return \Spryker\Shared\Transfer\TransferInterface
     */
    public function getKlarnaCheckoutHtml(QuoteTransfer $quoteTransfer)
    {
        return $this->getFactory()->createKlarnaStub()->getKlarnaCheckoutHtml($quoteTransfer);
    }

    /**
     * @api
     *
     * @author Daniel Bohnhardt <daniel.bohnhardt@twt.de>
     *
     * @param string $orderId
     *
     * @return void
     */
    public function storeKlarnaOrderIdInSession($orderId)
    {
        $this->getSession()->setKlarnaOrderId($orderId);
    }

    /**
     * @api
     *
     * @author Daniel Bohnhardt <daniel.bohnhardt@twt.de>
     *
     * @return string
     */
    public function getKlarnaOrderIdFromSession()
    {
        return $this->getSession()->getKlarnaOrderId();
    }

    /**
     * @api
     *
     * @author Daniel Bohnhardt <daniel.bohnhardt@twt.de>
     *
     * @return bool
     */
    public function removeKlarnaOrderIdFromSession()
    {
        return $this->getSession()->removeOrderId();
    }

    /**
     * @api
     *
     * @author Daniel Bohnhardt <daniel.bohnhardt@twt.de>
     *
     * @return bool
     */
    public function hasKlarnaOrderIdInSession()
    {
        return $this->getSession()->hasKlarnaOrderId();
    }

    /**
     * @api
     *
     * @author Daniel Bohnhardt <daniel.bohnhardt@twt.de>
     *
     * @param \Generated\Shared\Transfer\KlarnaCheckoutTransfer $klarnaCheckoutTransfer
     *
     * @return \Generated\Shared\Transfer\KlarnaCheckoutTransfer
     */
    public function getSuccessHtml(KlarnaCheckoutTransfer $klarnaCheckoutTransfer)
    {
        return $this->getFactory()->createKlarnaStub()->getSuccessHtml($klarnaCheckoutTransfer);
    }

    /**
     * @api
     *
     * @author Daniel Bohnhardt <daniel.bohnhardt@twt.de>
     *
     * @param \Generated\Shared\Transfer\KlarnaCheckoutTransfer $klarnaCheckoutTransfer
     *
     * @return mixed
     */
    public function createCheckoutOrder(KlarnaCheckoutTransfer $klarnaCheckoutTransfer)
    {
        return $this->getFactory()->createKlarnaStub()->createCheckoutOrder($klarnaCheckoutTransfer);
    }

    /**
     * @api
     *
     * @param \Generated\Shared\Transfer\KlarnaCheckoutServiceRequestTransfer $klarnaCheckoutServiceRequestTransfer
     *
     * @return \Generated\Shared\Transfer\KlarnaCheckoutServiceResponseTransfer
     */
    public function checkoutService(KlarnaCheckoutServiceRequestTransfer $klarnaCheckoutServiceRequestTransfer)
    {
        return $this->getFactory()->createKlarnaStub()->checkoutService($klarnaCheckoutServiceRequestTransfer);
    }

    /**
     * @api
     *
     * @param \Generated\Shared\Transfer\KlarnaGetAddressesRequestTransfer $klarnaGetAddressesRequestTransfer
     *
     * @return \Generated\Shared\Transfer\KlarnaGetAddressesResponseTransfer
     */
    public function getAddresses(KlarnaGetAddressesRequestTransfer $klarnaGetAddressesRequestTransfer)
    {
        return $this->getFactory()->createKlarnaStub()->getAddresses($klarnaGetAddressesRequestTransfer);
    }
}
