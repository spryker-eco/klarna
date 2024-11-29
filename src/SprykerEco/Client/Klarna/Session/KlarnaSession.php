<?php

/**
 * MIT License
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
     * @author Daniel Bohnhardt <daniel.bohnhardt@twt.de>
     *
     * @param \Generated\Shared\Transfer\KlarnaInstallmentResponseTransfer $responseTransfer
     *
     * @return $this
     */
    public function setInstallments(KlarnaInstallmentResponseTransfer $responseTransfer)
    {
        $this->session->set(self::KLARNA_INSTALLMENT_SESSION_IDENTIFIER, $responseTransfer);

        return $this;
    }

    /**
     * @author Daniel Bohnhardt <daniel.bohnhardt@twt.de>
     *
     * @return bool
     */
    public function hasInstallments()
    {
        return $this->session->has(self::KLARNA_INSTALLMENT_SESSION_IDENTIFIER);
    }

    /**
     * @author Daniel Bohnhardt <daniel.bohnhardt@twt.de>
     *
     * @return \Generated\Shared\Transfer\KlarnaInstallmentResponseTransfer
     */
    public function getInstallments()
    {
        $default = new KlarnaInstallmentResponseTransfer();

        return $this->session->get(self::KLARNA_INSTALLMENT_SESSION_IDENTIFIER, $default);
    }

    /**
     * @author Daniel Bohnhardt <daniel.bohnhardt@twt.de>
     *
     * @return bool
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
     * @author Daniel Bohnhardt <daniel.bohnhardt@twt.de>
     *
     * @return bool
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
     * @author Daniel Bohnhardt <daniel.bohnhardt@twt.de>
     *
     * @return string
     */
    public function getKlarnaOrderId()
    {
        return $this->session->get(self::KLARNA_ORDERID_SESSION_IDENTIFIER);
    }

    /**
     * @author Daniel Bohnhardt <daniel.bohnhardt@twt.de>
     *
     * @param string $orderId
     *
     * @return $this
     */
    public function setKlarnaOrderId($orderId)
    {
         $this->session->set(self::KLARNA_ORDERID_SESSION_IDENTIFIER, $orderId);

        return $this;
    }

    /**
     * @author Daniel Bohnhardt <daniel.bohnhardt@twt.de>
     *
     * @return bool
     */
    public function hasKlarnaOrderId()
    {
        return $this->session->has(self::KLARNA_ORDERID_SESSION_IDENTIFIER);
    }

}
