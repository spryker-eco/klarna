<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Zed\Klarna\Business\Request;

use Generated\Shared\Transfer\KlarnaPClassRequestTransfer;
use Generated\Shared\Transfer\QuoteTransfer;
use Spryker\Shared\Library\Currency\CurrencyManager;
use Spryker\Zed\Klarna\Business\Api\Handler\KlarnaApi;
use Spryker\Zed\Klarna\Business\Request\Mapper\PClassRequestTransferMapper;
use Spryker\Zed\Klarna\Business\Response\Mapper\InstallmentTransferMapper;

/**
 * Class Installment
 *
 * @package Spryker\Zed\Klarna\Business\Payment
 *
 * @author Daniel Bohnhardt <daniel.bohnhardt@twt.de>
 */
class InstallmentRequest
{

    /**
     * @var \Spryker\Zed\Klarna\Business\Api\Handler\KlarnaApi
     *
     * @author Daniel Bohnhardt <daniel.bohnhardt@twt.de>
     */
    protected $klarnaApi;

    /**
     * @var \Spryker\Zed\Klarna\Business\Request\Mapper\PClassRequestTransferMapper
     */
    protected $pClassRequestTransferMapper;

    /**
     * @var \Spryker\Zed\Klarna\Business\Response\Mapper\InstallmentTransferMapper
     */
    protected $installmentTransferMapper;

    /**
     * @var \Spryker\Shared\Library\Currency\CurrencyManager
     *
     * @author Daniel Bohnhardt <daniel.bohnhardt@twt.de>
     */
    protected $currencyManager;

    /**
     * Installment constructor.
     *
     * @param \Spryker\Zed\Klarna\Business\Api\Handler\KlarnaApi $klarnaApi
     * @param \Spryker\Zed\Klarna\Business\Request\Mapper\PClassRequestTransferMapper $pClassRequestTransferMapper
     * @param \Spryker\Zed\Klarna\Business\Response\Mapper\InstallmentTransferMapper $installmentTransferMapper
     *
     * @author Daniel Bohnhardt <daniel.bohnhardt@twt.de>
     */
    public function __construct(
        KlarnaApi $klarnaApi,
        PClassRequestTransferMapper $pClassRequestTransferMapper,
        InstallmentTransferMapper $installmentTransferMapper
    ) {
        $this->klarnaApi = $klarnaApi;
        $this->pClassRequestTransferMapper = $pClassRequestTransferMapper;
        $this->installmentTransferMapper = $installmentTransferMapper;
        $this->currencyManager = CurrencyManager::getInstance();
    }

    /**
     * @param \Generated\Shared\Transfer\QuoteTransfer $quoteTransfer
     *
     * @return \Generated\Shared\Transfer\KlarnaInstallmentResponseTransfer
     * @author Daniel Bohnhardt <daniel.bohnhardt@twt.de>
     */
    public function getInstallments(QuoteTransfer $quoteTransfer)
    {
        $klarnaPClassRequestTransfer = new KlarnaPClassRequestTransfer();
        $this->pClassRequestTransferMapper->map($klarnaPClassRequestTransfer, $quoteTransfer);

        $pClasses = $this->klarnaApi->getPclasses($klarnaPClassRequestTransfer);

        $klarnaInstallmentResponseTransfer = $this->installmentTransferMapper->map(
            $quoteTransfer,
            $pClasses,
            $this->currencyManager
        );

        return $klarnaInstallmentResponseTransfer;
    }

}
