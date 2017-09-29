<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Client\Klarna\Session;

use Generated\Shared\Transfer\KlarnaInstallmentResponseTransfer;

/**
 * Class KlarnaSession
 *
 * @package Spryker\Client\Klarna\Session
 *
 * @author Daniel Bohnhardt <daniel.bohnhardt@twt.de>
 */
interface KlarnaSessionInterface
{

    /**
     * @param \Generated\Shared\Transfer\KlarnaInstallmentResponseTransfer $responseTransfer
     *
     * @return $this
     * @author Daniel Bohnhardt <daniel.bohnhardt@twt.de>
     */
    public function setInstallments(KlarnaInstallmentResponseTransfer $responseTransfer);

    /**
     * @return bool
     * @author Daniel Bohnhardt <daniel.bohnhardt@twt.de>
     */
    public function hasInstallments();

    /**
     * @return \Generated\Shared\Transfer\KlarnaInstallmentResponseTransfer
     * @author Daniel Bohnhardt <daniel.bohnhardt@twt.de>
     */
    public function getInstallments();

    /**
     * @return bool
     * @author Daniel Bohnhardt <daniel.bohnhardt@twt.de>
     */
    public function removeInstallments();

    /**
     * @return bool
     * @author Daniel Bohnhardt <daniel.bohnhardt@twt.de>
     */
    public function removeOrderId();

    /**
     * @return string
     * @author Daniel Bohnhardt <daniel.bohnhardt@twt.de>
     */
    public function getKlarnaOrderId();

    /**
     * @param string $orderId
     *
     * @return $this
     * @author Daniel Bohnhardt <daniel.bohnhardt@twt.de>
     */
    public function setKlarnaOrderId($orderId);

    /**
     * @return bool
     * @author Daniel Bohnhardt <daniel.bohnhardt@twt.de>
     */
    public function hasKlarnaOrderId();

}
