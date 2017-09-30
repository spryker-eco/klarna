<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace SprykerEco\Client\Klarna\Session;

use Generated\Shared\Transfer\KlarnaInstallmentResponseTransfer;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

/**
 * Class KlarnaSession
 *
 * @package SprykerEco\Client\Klarna\Session
 *
 * @author Daniel Bohnhardt <daniel.bohnhardt@twt.de>
 */
class KlarnaSession implements KlarnaSessionInterface
{

    const KLARNA_INSTALLMENT_SESSION_IDENTIFIER = 'klarna_installment_session_identifier';

    const KLARNA_ORDERID_SESSION_IDENTIFIER = 'klarna_orderid_session_identifier';

    /**
     * @var \Symfony\Component\HttpFoundation\Session\SessionInterface
     */
    private $session;

    /**
     * @param \Symfony\Component\HttpFoundation\Session\SessionInterface $session
     */
    public function __construct(SessionInterface $session)
    {
        $this->session = $session;
    }

    /**
     * @param \Generated\Shared\Transfer\KlarnaInstallmentResponseTransfer $responseTransfer
     *
     * @return $this
     * @author Daniel Bohnhardt <daniel.bohnhardt@twt.de>
     */
    public function setInstallments(KlarnaInstallmentResponseTransfer $responseTransfer)
    {
        $this->session->set(self::KLARNA_INSTALLMENT_SESSION_IDENTIFIER, $responseTransfer);

        return $this;
    }

    /**
     * @return bool
     * @author Daniel Bohnhardt <daniel.bohnhardt@twt.de>
     */
    public function hasInstallments()
    {
        return $this->session->has(self::KLARNA_INSTALLMENT_SESSION_IDENTIFIER);
    }

    /**
     * @return \Generated\Shared\Transfer\KlarnaInstallmentResponseTransfer
     * @author Daniel Bohnhardt <daniel.bohnhardt@twt.de>
     */
    public function getInstallments()
    {
        $default = new KlarnaInstallmentResponseTransfer();

        return $this->session->get(self::KLARNA_INSTALLMENT_SESSION_IDENTIFIER, $default);
    }

    /**
     * @return bool
     * @author Daniel Bohnhardt <daniel.bohnhardt@twt.de>
     */
    public function removeInstallments()
    {
        if ($this->hasInstallments()) {
            $this->session->remove(self::KLARNA_INSTALLMENT_SESSION_IDENTIFIER);

            return true;
        }

        return false;
    }

    /**
     * @return bool
     * @author Daniel Bohnhardt <daniel.bohnhardt@twt.de>
     */
    public function removeOrderId()
    {
        if ($this->hasKlarnaOrderId()) {
            $this->session->remove(self::KLARNA_ORDERID_SESSION_IDENTIFIER);

            return true;
        }

        return false;
    }

    /**
     * @return string
     * @author Daniel Bohnhardt <daniel.bohnhardt@twt.de>
     */
    public function getKlarnaOrderId()
    {
        return $this->session->get(self::KLARNA_ORDERID_SESSION_IDENTIFIER);
    }

    /**
     * @param string $orderId
     *
     * @return $this
     * @author Daniel Bohnhardt <daniel.bohnhardt@twt.de>
     */
    public function setKlarnaOrderId($orderId)
    {
         $this->session->set(self::KLARNA_ORDERID_SESSION_IDENTIFIER, $orderId);

        return $this;
    }

    /**
     * @return bool
     * @author Daniel Bohnhardt <daniel.bohnhardt@twt.de>
     */
    public function hasKlarnaOrderId()
    {
        return $this->session->has(self::KLARNA_ORDERID_SESSION_IDENTIFIER);
    }

}
