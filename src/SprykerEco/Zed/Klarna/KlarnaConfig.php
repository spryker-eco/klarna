<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace SprykerEco\Zed\Klarna;

use SprykerEco\Shared\Klarna\KlarnaConstants;
use Spryker\Zed\Kernel\AbstractBundleConfig;

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
     * @return string
     * @author Daniel Bohnhardt <daniel.bohnhardt@twt.de>
     */
    public function getSharedSecret()
    {
        return $this->get(KlarnaConstants::SHARED_SECRED);
    }

    /**
     * @return string
     * @author Daniel Bohnhardt <daniel.bohnhardt@twt.de>
     */
    public function getEid()
    {
        return $this->get(KlarnaConstants::EID);
    }

    /**
     * @return bool
     * @author Daniel Bohnhardt <daniel.bohnhardt@twt.de>
     */
    public function isTestMode()
    {
        return $this->get(KlarnaConstants::TEST_MODE);
    }

    /**
     * @return string
     * @author Daniel Bohnhardt <daniel.bohnhardt@twt.de>
     */
    public function getMailMode()
    {
        return $this->get(KlarnaConstants::KLARNA_INVOICE_MAIL_TYPE);
    }

    /**
     * @return string
     * @author Daniel Bohnhardt <daniel.bohnhardt@twt.de>
     */
    public function getPclassStoreType()
    {
        return $this->get(KlarnaConstants::KLARNA_PCLASS_STORE_TYPE);
    }

    /**
     * @return string
     * @author Daniel Bohnhardt <daniel.bohnhardt@twt.de>
     */
    public function getPclassStoreUri()
    {
        return $this->get(KlarnaConstants::KLARNA_PCLASS_STORE_URI);
    }

    /**
     * @return string
     * @author Daniel Bohnhardt <daniel.bohnhardt@twt.de>
     */
    public function getCheckoutConfirmationUri()
    {
        return $this->get(KlarnaConstants::KLARNA_CHECKOUT_CONFIRMATION_URI);
    }

    /**
     * @return string
     * @author Daniel Bohnhardt <daniel.bohnhardt@twt.de>
     */
    public function getCheckoutTermsUri()
    {
        return $this->get(KlarnaConstants::KLARNA_CHECKOUT_TERMS_URI);
    }

    /**
     * @return string
     * @author Daniel Bohnhardt <daniel.bohnhardt@twt.de>
     */
    public function getCheckoutPushUri()
    {
        return $this->get(KlarnaConstants::KLARNA_CHECKOUT_PUSH_URI);
    }

    /**
     * @return string
     * @author Daniel Bohnhardt <daniel.bohnhardt@twt.de>
     */
    public function getCheckoutUri()
    {
        return $this->get(KlarnaConstants::KLARNA_CHECKOUT_URI);
    }

    /**
     * @return string
     * @author Daniel Bohnhardt <daniel.bohnhardt@twt.de>
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
