<?php

/**
 * MIT License
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace SprykerEco\Client\Klarna;

use Spryker\Client\Kernel\AbstractFactory;
use SprykerEco\Client\Klarna\Session\KlarnaSession;
use SprykerEco\Client\Klarna\Zed\KlarnaStub;

/**
 * Class KlarnaFactory
 *
 * @package SprykerEco\Client\Klarna
 *
 * @author Daniel Bohnhardt <daniel.bohnhardt@twt.de>
 */
class KlarnaFactory extends AbstractFactory
{

    /**
     * @return \SprykerEco\Client\Klarna\Zed\KlarnaStub
     */
    public function createKlarnaStub()
    {
        return new KlarnaStub($this->getZedService());
    }

    /**
     * @author Daniel Bohnhardt <daniel.bohnhardt@twt.de>
     *
     * @return \SprykerEco\Client\Klarna\Session\KlarnaSession
     */
    public function createKlarnaSession()
    {
        return new KlarnaSession($this->getSessionClient());
    }

    /**
     * @return mixed
     */
    protected function getZedService()
    {
        return $this->getProvidedDependency(KlarnaDependencyProvider::SERVICE_ZED);
    }

    /**
     * @return mixed
     */
    protected function getSessionClient()
    {
        return $this->getProvidedDependency(KlarnaDependencyProvider::CLIENT_SESSION);
    }

}
