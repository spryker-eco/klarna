<?php

/**
 * MIT License
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace SprykerEco\Client\Klarna\Session;

use Generated\Shared\Transfer\KlarnaInstallmentResponseTransfer;

/**
 * Class KlarnaSession
 *
 * @package SprykerEco\Client\Klarna\Session
 *
 * @author Daniel Bohnhardt <daniel.bohnhardt@twt.de>
 */
interface KlarnaSessionInterface
{
    /**
     * @author Daniel Bohnhardt <daniel.bohnhardt@twt.de>
     *
     * @param \Generated\Shared\Transfer\KlarnaInstallmentResponseTransfer $responseTransfer
     *
     * @return $this
     */
    public function setInstallments(KlarnaInstallmentResponseTransfer $responseTransfer);

    /**
     * @author Daniel Bohnhardt <daniel.bohnhardt@twt.de>
     *
     * @return bool
     */
    public function hasInstallments();

    /**
     * @author Daniel Bohnhardt <daniel.bohnhardt@twt.de>
     *
     * @return \Generated\Shared\Transfer\KlarnaInstallmentResponseTransfer
     */
    public function getInstallments();

    /**
     * @author Daniel Bohnhardt <daniel.bohnhardt@twt.de>
     *
     * @return bool
     */
    public function removeInstallments();

    /**
     * @author Daniel Bohnhardt <daniel.bohnhardt@twt.de>
     *
     * @return bool
     */
    public function removeOrderId();

    /**
     * @author Daniel Bohnhardt <daniel.bohnhardt@twt.de>
     *
     * @return string
     */
    public function getKlarnaOrderId();

    /**
     * @author Daniel Bohnhardt <daniel.bohnhardt@twt.de>
     *
     * @param string $orderId
     *
     * @return $this
     */
    public function setKlarnaOrderId($orderId);

    /**
     * @author Daniel Bohnhardt <daniel.bohnhardt@twt.de>
     *
     * @return bool
     */
    public function hasKlarnaOrderId();
}
