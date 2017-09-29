<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Zed\Klarna\Communication;

use Spryker\Zed\Kernel\Communication\AbstractCommunicationFactory;
use Spryker\Zed\Klarna\Communication\Table\Payments;
use Spryker\Zed\Klarna\Communication\Table\StatusLog;
use Spryker\Zed\Klarna\KlarnaDependencyProvider;

/**
 * Class KlarnaCommunicationFactory
 *
 * @package Spryker\Zed\Klarna\Communication
 *
 * @author Daniel Bohnhardt <daniel.bohnhardt@twt.de>
 * @method \Spryker\Zed\Klarna\Persistence\KlarnaQueryContainerInterface getQueryContainer()
 * @method \Spryker\Zed\Klarna\KlarnaConfig getConfig()
 */
class KlarnaCommunicationFactory extends AbstractCommunicationFactory
{

    /**
     * @return \Spryker\Zed\Klarna\Communication\Table\Payments
     */
    public function createPaymentsTable()
    {
        $paymentKlarnaQuery = $this->getQueryContainer()->queryPayments();

        return new Payments($paymentKlarnaQuery);
    }

    /**
     * @param int $idPayment
     *
     * @return \Spryker\Zed\Klarna\Communication\Table\StatusLog
     * @author Daniel Bohnhardt <daniel.bohnhardt@twt.de>
     */
    public function createStatusLogTable($idPayment)
    {
        $statusLogQuery = $this->getQueryContainer()->queryTransactionStatusLogByPaymentId($idPayment);

        return new StatusLog($statusLogQuery, $idPayment);
    }

    /**
     * @return \Spryker\Zed\Klarna\Dependency\Facade\KlarnaToSalesInterface
     */
    public function getSalesFacade()
    {
        return $this->getProvidedDependency(KlarnaDependencyProvider::FACADE_SALES);
    }

}
