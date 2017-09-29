<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Functional\Spryker\Zed\Klarna\Business;

use Codeception\TestCase\Test;
use Spryker\Zed\Klarna\KlarnaConfig;
use Spryker\Zed\Klarna\Persistence\KlarnaQueryContainer;

/**
 * Class AbstractFacadeTest
 *
 * @package Functional\Spryker\Zed\Klarna\Business\Order
 *
 * @author Daniel Bohnhardt <daniel.bohnhardt@twt.de>
 */
class AbstractFacadeTest extends Test
{

    /**
     * @return \Spryker\Zed\Klarna\Business\KlarnaFacade
     */
    public function generateFacade($adapter)
    {
        $localeBridge = $this->getMockBuilder('Spryker\Zed\Klarna\Dependency\Facade\KlarnaToLocaleBridge')
            ->disableOriginalConstructor()
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

        // Business factory always requires a valid query container. Since we're creating
        // functional/integration tests there's no need to mock the database layer.
        $queryContainer = new KlarnaQueryContainer();
        $businessFactoryMock->setQueryContainer($queryContainer);

        // Mock the facade to override getFactory() and have it return out
        // previously created mock.
        $facade = $this->getMock(
            'Spryker\Zed\Klarna\Business\KlarnaFacade',
            ['getFactory']
        );
        $facade->expects($this->any())
               ->method('getFactory')
               ->will($this->returnValue($businessFactoryMock));

        return $facade;
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject|\Spryker\Zed\Klarna\Business\KlarnaBusinessFactory
     */
    protected function getBusinessFactoryMock()
    {
        $businessFactoryMock = $this->getMock(
            'Spryker\Zed\Klarna\Business\KlarnaBusinessFactory',
            ['createAdapter', 'getLocaleFacade']
        );

        return $businessFactoryMock;
    }

}
