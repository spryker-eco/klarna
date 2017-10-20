<?php

/**
 * MIT License
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace SprykerEcoTest\Zed\Klarna\Business;

use Codeception\TestCase\Test;
use SprykerEco\Zed\Klarna\Business\KlarnaBusinessFactory;
use SprykerEco\Zed\Klarna\Business\KlarnaFacade;
use SprykerEco\Zed\Klarna\Dependency\Facade\KlarnaToLocaleInterface;
use SprykerEco\Zed\Klarna\Dependency\Facade\KlarnaToMoneyInterface;
use SprykerEco\Zed\Klarna\KlarnaConfig;
use SprykerEco\Zed\Klarna\Persistence\KlarnaQueryContainer;

class AbstractFacadeTest extends Test
{
    /**
     * @param \SprykerEcoTest\Zed\Klarna\Business\Api\Mock\KlarnaApiMockAbstract $adapter
     *
     * @return \SprykerEco\Zed\Klarna\Business\KlarnaFacade
     */
    public function generateFacade($adapter)
    {
        $localeBridge = $this->getMockBuilder(KlarnaToLocaleInterface::class)
            ->setMethods(['getCurrentLocaleName'])
            ->getMock();
        $localeBridge->expects($this->any())
            ->method('getCurrentLocaleName')
            ->willReturn('de_DE');

        // Mock business factory to override return value of createExecutionAdapter to
        // place a mocked adapter that doesn't establish an actual connection.
        $businessFactoryMock = self::getBusinessFactoryMock();
        $businessFactoryMock->setConfig(new KlarnaConfig());
        $businessFactoryMock
            ->expects($this->any())
            ->method('createAdapter')
            ->will($this->returnValue($adapter));
        $businessFactoryMock
            ->method('getLocaleFacade')
            ->willReturn($localeBridge);

        $moneyFacade = $this->getMockBuilder(KlarnaToMoneyInterface::class)
            ->setMethods(['convertIntegerToDecimal', 'convertDecimalToInteger'])
            ->getMock();

        $businessFactoryMock
            ->method('getMoneyFacade')
            ->willReturn($moneyFacade);

        // Business factory always requires a valid query container. Since we're creating
        // functional/integration tests there's no need to mock the database layer.
        $queryContainer = new KlarnaQueryContainer();
        $businessFactoryMock->setQueryContainer($queryContainer);

        // Mock the facade to override getFactory() and have it return out
        // previously created mock.
        $facade = $this->getMockBuilder(KlarnaFacade::class)
            ->setMethods(['getFactory'])
            ->getMock();
        $facade->expects($this->any())
               ->method('getFactory')
               ->will($this->returnValue($businessFactoryMock));

        return $facade;
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject|\SprykerEco\Zed\Klarna\Business\KlarnaBusinessFactory
     */
    protected function getBusinessFactoryMock()
    {
        $businessFactoryMock = $this->getMockBuilder(KlarnaBusinessFactory::class)
            ->setMethods(['createAdapter', 'getLocaleFacade', 'getMoneyFacade'])
            ->getMock();

        return $businessFactoryMock;
    }
}
