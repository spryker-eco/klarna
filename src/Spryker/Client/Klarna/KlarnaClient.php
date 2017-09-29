<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Client\Klarna;

use Generated\Shared\Transfer\KlarnaCheckoutServiceRequestTransfer;
use Generated\Shared\Transfer\KlarnaCheckoutTransfer;
use Generated\Shared\Transfer\KlarnaGetAddressesRequestTransfer;
use Generated\Shared\Transfer\KlarnaInstallmentResponseTransfer;
use Generated\Shared\Transfer\QuoteTransfer;
use Spryker\Client\Kernel\AbstractClient;

/**
 * Class KlarnaClient
 *
 * @package Spryker\Client\Klarna
 *
 * @author Daniel Bohnhardt <daniel.bohnhardt@twt.de>
 * @method \Spryker\Client\Klarna\KlarnaFactory getFactory()
 */
class KlarnaClient extends AbstractClient implements KlarnaClientInterface
{

    /**
     * @api
     * @param \Generated\Shared\Transfer\QuoteTransfer $quoteTransfer
     *
     * @return \Generated\Shared\Transfer\KlarnaReserveAmountResponseTransfer
     * @author Daniel Bohnhardt <daniel.bohnhardt@twt.de>
     */
    public function updatePayment(QuoteTransfer $quoteTransfer)
    {
        return $this->getFactory()->createKlarnaStub()->updatePayment($quoteTransfer);
    }

    /**
     * @api
     * @param \Generated\Shared\Transfer\QuoteTransfer $quoteTransfer
     *
     * @return \Generated\Shared\Transfer\KlarnaInstallmentResponseTransfer
     * @author Daniel Bohnhardt <daniel.bohnhardt@twt.de>
     */
    public function getInstallments(QuoteTransfer $quoteTransfer)
    {
        return $this->getFactory()->createKlarnaStub()->getInstallments($quoteTransfer);
    }

    /**
     * @api
     * @return \Spryker\Client\Klarna\Session\KlarnaSession
     * @author Daniel Bohnhardt <daniel.bohnhardt@twt.de>
     */
    public function getSession()
    {
        return $this->getFactory()->createKlarnaSession();
    }

    /**
     * @api
     * @return bool
     * @author Daniel Bohnhardt <daniel.bohnhardt@twt.de>
     */
    public function hasInstallmentsInSession()
    {
        return $this->getSession()->hasInstallments();
    }

    /**
     * @api
     * @param \Generated\Shared\Transfer\KlarnaInstallmentResponseTransfer $installmentResponseTransfer
     *
     * @return \Generated\Shared\Transfer\KlarnaInstallmentResponseTransfer
     * @author Daniel Bohnhardt <daniel.bohnhardt@twt.de>
     */
    public function storeInstallmentsInSession(KlarnaInstallmentResponseTransfer $installmentResponseTransfer)
    {
        $this->getSession()->setInstallments($installmentResponseTransfer);

        return $installmentResponseTransfer;
    }

    /**
     * @api
     * @return bool
     * @author Daniel Bohnhardt <daniel.bohnhardt@twt.de>
     */
    public function removeInstallmentsFromSession()
    {
        return $this->getSession()->removeInstallments();
    }

    /**
     * @api
     * @return \Generated\Shared\Transfer\KlarnaInstallmentResponseTransfer
     * @author Daniel Bohnhardt <daniel.bohnhardt@twt.de>
     */
    public function getInstallmentsFromSession()
    {
        return $this->getSession()->getInstallments();
    }

    /**
     * @api
     * @param \Generated\Shared\Transfer\QuoteTransfer $quoteTransfer
     *
     * @return \Spryker\Shared\Transfer\TransferInterface
     * @author Daniel Bohnhardt <daniel.bohnhardt@twt.de>
     */
    public function getKlarnaCheckoutHtml(QuoteTransfer $quoteTransfer)
    {
        return $this->getFactory()->createKlarnaStub()->getKlarnaCheckoutHtml($quoteTransfer);
    }

    /**
     * @api
     * @param string $orderId
     * @return void
     * @author Daniel Bohnhardt <daniel.bohnhardt@twt.de>
     */
    public function storeKlarnaOrderIdInSession($orderId)
    {
        $this->getSession()->setKlarnaOrderId($orderId);
    }

    /**
     * @api
     * @return string
     * @author Daniel Bohnhardt <daniel.bohnhardt@twt.de>
     */
    public function getKlarnaOrderIdFromSession()
    {
        return $this->getSession()->getKlarnaOrderId();
    }

    /**
     * @api
     * @return bool
     * @author Daniel Bohnhardt <daniel.bohnhardt@twt.de>
     */
    public function removeKlarnaOrderIdFromSession()
    {
        return $this->getSession()->removeOrderId();
    }

    /**
     * @api
     * @return bool
     * @author Daniel Bohnhardt <daniel.bohnhardt@twt.de>
     */
    public function hasKlarnaOrderIdInSession()
    {
        return $this->getSession()->hasKlarnaOrderId();
    }

    /**
     * @api
     * @param \Generated\Shared\Transfer\KlarnaCheckoutTransfer $klarnaCheckoutTransfer
     *
     * @return \Generated\Shared\Transfer\KlarnaCheckoutTransfer
     * @author Daniel Bohnhardt <daniel.bohnhardt@twt.de>
     */
    public function getSuccessHtml(KlarnaCheckoutTransfer $klarnaCheckoutTransfer)
    {
        return $this->getFactory()->createKlarnaStub()->getSuccessHtml($klarnaCheckoutTransfer);
    }

    /**
     * @api
     * @param \Generated\Shared\Transfer\KlarnaCheckoutTransfer $klarnaCheckoutTransfer
     *
     * @return mixed
     * @author Daniel Bohnhardt <daniel.bohnhardt@twt.de>
     */
    public function createCheckoutOrder(KlarnaCheckoutTransfer $klarnaCheckoutTransfer)
    {
        return $this->getFactory()->createKlarnaStub()->createCheckoutOrder($klarnaCheckoutTransfer);
    }

    /**
     * @api
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
     * @param \Generated\Shared\Transfer\KlarnaGetAddressesRequestTransfer $klarnaGetAddressesRequestTransfer
     *
     * @return \Generated\Shared\Transfer\KlarnaGetAddressesResponseTransfer
     */
    public function getAddresses(KlarnaGetAddressesRequestTransfer $klarnaGetAddressesRequestTransfer)
    {
        return $this->getFactory()->createKlarnaStub()->getAddresses($klarnaGetAddressesRequestTransfer);
    }

}
