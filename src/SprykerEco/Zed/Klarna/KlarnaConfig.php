<?php

/**
 * MIT License
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace SprykerEco\Zed\Klarna;

use Spryker\Zed\Kernel\AbstractBundleConfig;
use SprykerEco\Shared\Klarna\KlarnaConstants;

/**
 * Class KlarnaConfig
 *
 * @package SprykerEco\Zed\Klarna
 *
 * @author Daniel Bohnhardt <daniel.bohnhardt@twt.de>
 */
class KlarnaConfig extends AbstractBundleConfig
{
    /**
     * @author Daniel Bohnhardt <daniel.bohnhardt@twt.de>
     *
     * @return string
     */
    public function getSharedSecret()
    {
        return $this->get(KlarnaConstants::SHARED_SECRET);
    }

    /**
     * @author Daniel Bohnhardt <daniel.bohnhardt@twt.de>
     *
     * @return string
     */
    public function getEid()
    {
        return $this->get(KlarnaConstants::EID);
    }

    /**
     * @author Daniel Bohnhardt <daniel.bohnhardt@twt.de>
     *
     * @return bool
     */
    public function isTestMode()
    {
        return $this->get(KlarnaConstants::TEST_MODE);
    }

    /**
     * @author Daniel Bohnhardt <daniel.bohnhardt@twt.de>
     *
     * @return string
     */
    public function getMailMode()
    {
        return $this->get(KlarnaConstants::KLARNA_INVOICE_MAIL_TYPE);
    }

    /**
     * @author Daniel Bohnhardt <daniel.bohnhardt@twt.de>
     *
     * @return string
     */
    public function getPclassStoreType()
    {
        return $this->get(KlarnaConstants::KLARNA_PCLASS_STORE_TYPE);
    }

    /**
     * @author Daniel Bohnhardt <daniel.bohnhardt@twt.de>
     *
     * @return string
     */
    public function getPclassStoreUri()
    {
        return $this->get(KlarnaConstants::KLARNA_PCLASS_STORE_URI);
    }

    /**
     * @author Daniel Bohnhardt <daniel.bohnhardt@twt.de>
     *
     * @return string
     */
    public function getCheckoutConfirmationUri()
    {
        return $this->get(KlarnaConstants::KLARNA_CHECKOUT_CONFIRMATION_URI);
    }

    /**
     * @author Daniel Bohnhardt <daniel.bohnhardt@twt.de>
     *
     * @return string
     */
    public function getCheckoutTermsUri()
    {
        return $this->get(KlarnaConstants::KLARNA_CHECKOUT_TERMS_URI);
    }

    /**
     * @author Daniel Bohnhardt <daniel.bohnhardt@twt.de>
     *
     * @return string
     */
    public function getCheckoutPushUri()
    {
        return $this->get(KlarnaConstants::KLARNA_CHECKOUT_PUSH_URI);
    }

    /**
     * @author Daniel Bohnhardt <daniel.bohnhardt@twt.de>
     *
     * @return string
     */
    public function getCheckoutUri()
    {
        return $this->get(KlarnaConstants::KLARNA_CHECKOUT_URI);
    }

    /**
     * @author Daniel Bohnhardt <daniel.bohnhardt@twt.de>
     *
     * @return string
     */
    public function getPdfUrlPattern()
    {
        return $this->get(KlarnaConstants::KLARNA_PDF_URL_PATTERN);
    }

    /**
     * @return array
     */
    public function getCurrency()
    {
        return $this->get(KlarnaConstants::CURRENCY);
    }
}
