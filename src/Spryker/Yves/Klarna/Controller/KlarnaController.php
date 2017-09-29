<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Yves\Klarna\Controller;

use Generated\Shared\Transfer\KlarnaCheckoutServiceRequestTransfer;
use Generated\Shared\Transfer\KlarnaCheckoutTransfer;
use Generated\Shared\Transfer\KlarnaGetAddressesRequestTransfer;
use Generated\Shared\Transfer\KlarnaObjectInitTransfer;
use Generated\Shared\Transfer\KlarnaPaymentTransfer;
use Generated\Shared\Transfer\PaymentTransfer;
use Generated\Shared\Transfer\QuoteTransfer;
use Generated\Shared\Transfer\TotalsTransfer;
use Pyz\Yves\Checkout\Plugin\Provider\CheckoutControllerProvider;
use Spryker\Yves\Application\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * Class KlarnaController
 *
 * @package Spryker\Yves\Klarna\Controller
 * @method \Spryker\Yves\Klarna\KlarnaFactory getFactory()
 *
 * @author Daniel Bohnhardt <daniel.bohnhardt@twt.de>
 */
class KlarnaController extends AbstractController
{

    /**
     * @param \Symfony\Component\HttpFoundation\Request $request
     *
     * @return array|\Symfony\Component\HttpFoundation\RedirectResponse
     * @author Daniel Bohnhardt <daniel.bohnhardt@twt.de>
     */
    public function successAction(Request $request)
    {
        $klarnaClient   = $this->getFactory()->getKlarnaClient();
        $requestOrderId = $request->query->get('klarna_order_id');
        if (!$requestOrderId ||
            !$klarnaClient->hasKlarnaOrderIdInSession() ||
            $requestOrderId !== $klarnaClient->getKlarnaOrderIdFromSession()
        ) {
            return $this->redirectResponseInternal(CheckoutControllerProvider::CHECKOUT_INDEX);
        }

        $klarnaCheckoutTransfer = new KlarnaCheckoutTransfer();
        $klarnaCheckoutTransfer->setOrderid($klarnaClient->getKlarnaOrderIdFromSession());

        $klarnaResponse = $klarnaClient->getSuccessHtml($klarnaCheckoutTransfer);

        $cartClient = $this->getFactory()->getCartClient();
        $cartClient->storeQuote(new QuoteTransfer());

        return [
            'html' => $klarnaResponse->getHtml()
        ];
    }

    /**
     * @param \Symfony\Component\HttpFoundation\Request $request
     *
     * @return \Symfony\Component\HttpFoundation\Response
     * @throws \Symfony\Component\HttpKernel\Exception\NotFoundHttpException
     * @author Daniel Bohnhardt <daniel.bohnhardt@twt.de>
     */
    public function pushAction(Request $request)
    {
        $requestOrderId = $request->query->get('klarna_order_id');
        if (!$requestOrderId) {
            throw new NotFoundHttpException();
        }

        $klarnaClient   = $this->getFactory()->getKlarnaClient();
        $klarnaCheckoutTransfer = new KlarnaCheckoutTransfer();
        $klarnaCheckoutTransfer->setOrderid($requestOrderId);

        $klarnaClient->createCheckoutOrder($klarnaCheckoutTransfer);

        return new Response();
    }


    /**
     * @param \Symfony\Component\HttpFoundation\Request $request
     *
     * @return array|\Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function getAddressesAction(Request $request)
    {
        $klarnaClient = $this->getFactory()->getKlarnaClient();
        // Example code, how to use get_addresses method
        $klarnaObjectInitTransfer = new KlarnaObjectInitTransfer();
        $klarnaObjectInitTransfer->setCurrencyIso3Code('SEK');
        $klarnaObjectInitTransfer->setIso2Code('SE');
        $klarnaGetAddressesRequestTransfer = new KlarnaGetAddressesRequestTransfer();
        $klarnaGetAddressesRequestTransfer->setKlarnaObjectInit($klarnaObjectInitTransfer);
        $klarnaGetAddressesRequestTransfer->setPno('410321-9202');

        $klarnaGetAddressesResponseTransfer = $klarnaClient->getAddresses($klarnaGetAddressesRequestTransfer);

        return [
            'addresses' => $klarnaGetAddressesResponseTransfer
        ];
    }

    /**
     * @param \Symfony\Component\HttpFoundation\Request $request
     *
     * @return array|\Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function checkoutServiceAction(Request $request)
    {
        $klarnaClient = $this->getFactory()->getKlarnaClient();
        // Example code, how to use checkoutService method
        $klarnaObjectInitTransfer = new KlarnaObjectInitTransfer();
        $klarnaObjectInitTransfer->setCurrencyIso3Code('SEK');
        $klarnaObjectInitTransfer->setIso2Code('SE');
        $klarnaObjectInitTransfer->setClientIp('194.122.82.53');
        $klarnaCheckoutServiceRequestTransfer = new KlarnaCheckoutServiceRequestTransfer();
        $klarnaCheckoutServiceRequestTransfer->setKlarnaObjectInit($klarnaObjectInitTransfer);
        $klarnaCheckoutServiceRequestTransfer->setGrandTotal(23000);

        $checkoutServiceTransfer = $klarnaClient->checkoutService($klarnaCheckoutServiceRequestTransfer);

        return [
            'checkoutService' => $checkoutServiceTransfer
        ];
    }

    /**
     * @param \Symfony\Component\HttpFoundation\Request $request
     *
     * @return array|\Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function installmentsAction(Request $request)
    {
        $pClassType = $request->query->get('pclass_type');
        $klarnaClient = $this->getFactory()->getKlarnaClient();

        // Example code, how to use get pClasses method
        $quoteTransfer = new QuoteTransfer();
        $paymentTransfer = new PaymentTransfer();
        $klarnaPaymentTransfer = new KlarnaPaymentTransfer();
        $totalsTransfer = new TotalsTransfer();
        $klarnaPaymentTransfer->setCurrencyIso3Code('SEK');
        $klarnaPaymentTransfer->setLanguageIso2Code('SE');
        $klarnaPaymentTransfer->setClientIp('194.122.82.53');
        $paymentTransfer->setKlarna($klarnaPaymentTransfer);
        $quoteTransfer->setPayment($paymentTransfer);
        $quoteTransfer->setKlarnaPClassType($pClassType);
        $totalsTransfer->setGrandTotal(23000);
        $quoteTransfer->setTotals($totalsTransfer);

        $installments = $klarnaClient->getInstallments($quoteTransfer);

        return [
            'installments' => $installments
        ];
    }

}
