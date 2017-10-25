<?php

/**
 * MIT License
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace SprykerEco\Zed\Klarna\Business\Request;

use Generated\Shared\Transfer\KlarnaPClassRequestTransfer;
use Generated\Shared\Transfer\QuoteTransfer;
use SprykerEco\Zed\Klarna\Business\Api\Handler\KlarnaApiInterface;
use SprykerEco\Zed\Klarna\Business\Request\Mapper\PClassRequestTransferMapperInterface;
use SprykerEco\Zed\Klarna\Business\Response\Mapper\InstallmentTransferMapperInterface;

class InstallmentRequest implements InstallmentRequestInterface
{
    /**
     * @var \SprykerEco\Zed\Klarna\Business\Api\Handler\KlarnaApiInterface
     */
    protected $klarnaApi;

    /**
     * @var \SprykerEco\Zed\Klarna\Business\Request\Mapper\PClassRequestTransferMapperInterface
     */
    protected $pClassRequestTransferMapper;

    /**
     * @var \SprykerEco\Zed\Klarna\Business\Response\Mapper\InstallmentTransferMapperInterface
     */
    protected $installmentTransferMapper;

    /**
     * @param \SprykerEco\Zed\Klarna\Business\Api\Handler\KlarnaApiInterface $klarnaApi
     * @param \SprykerEco\Zed\Klarna\Business\Request\Mapper\PClassRequestTransferMapperInterface $pClassRequestTransferMapper
     * @param \SprykerEco\Zed\Klarna\Business\Response\Mapper\InstallmentTransferMapperInterface $installmentTransferMapper
     */
    public function __construct(
        KlarnaApiInterface $klarnaApi,
        PClassRequestTransferMapperInterface $pClassRequestTransferMapper,
        InstallmentTransferMapperInterface $installmentTransferMapper
    ) {
        $this->klarnaApi = $klarnaApi;
        $this->pClassRequestTransferMapper = $pClassRequestTransferMapper;
        $this->installmentTransferMapper = $installmentTransferMapper;
    }

    /**
     * @param \Generated\Shared\Transfer\QuoteTransfer $quoteTransfer
     *
     * @return \Generated\Shared\Transfer\KlarnaInstallmentResponseTransfer
     */
    public function getInstallments(QuoteTransfer $quoteTransfer)
    {
        $klarnaPClassRequestTransfer = new KlarnaPClassRequestTransfer();
        $this->pClassRequestTransferMapper->map($klarnaPClassRequestTransfer, $quoteTransfer);

        $pClasses = $this->klarnaApi->getPclasses($klarnaPClassRequestTransfer);

        $klarnaInstallmentResponseTransfer = $this->installmentTransferMapper->map(
            $quoteTransfer,
            $pClasses
        );

        return $klarnaInstallmentResponseTransfer;
    }
}
