<?php

/**
 * MIT License
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace SprykerEco\Client\Klarna;

use Spryker\Client\Kernel\AbstractFactory;
use SprykerEco\Client\Klarna\Session\KlarnaSession;
use SprykerEco\Client\Klarna\Zed\KlarnaStub;

class KlarnaFactory extends AbstractFactory
{
    /**
     * @return \SprykerEco\Client\Klarna\Zed\KlarnaStubInterface
     */
    public function createKlarnaStub()
    {
        return new KlarnaStub($this->getZedService());
    }

    /**
     * @return \SprykerEco\Client\Klarna\Session\KlarnaSessionInterface
     */
    public function createKlarnaSession()
    {
        return new KlarnaSession($this->getSessionClient());
    }

    /**
     * @return \Spryker\Client\ZedRequest\ZedRequestClientInterface
     */
    protected function getZedService()
    {
        return $this->getProvidedDependency(KlarnaDependencyProvider::SERVICE_ZED);
    }

    /**
     * @return \Spryker\Client\Session\SessionClientInterface
     */
    protected function getSessionClient()
    {
        return $this->getProvidedDependency(KlarnaDependencyProvider::CLIENT_SESSION);
    }
}
