<?php

/**
 * MIT License
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace SprykerEco\Zed\Klarna\Communication;

use Spryker\Zed\Kernel\Communication\AbstractCommunicationFactory;
use SprykerEco\Zed\Klarna\Communication\Table\Payments;
use SprykerEco\Zed\Klarna\Communication\Table\StatusLog;
use SprykerEco\Zed\Klarna\KlarnaDependencyProvider;

/**
 * Class KlarnaCommunicationFactory
 *
 * @package SprykerEco\Zed\Klarna\Communication
 *
 * @author Daniel Bohnhardt <daniel.bohnhardt@twt.de>
 * @method \SprykerEco\Zed\Klarna\Persistence\KlarnaQueryContainerInterface getQueryContainer()
 * @method \SprykerEco\Zed\Klarna\KlarnaConfig getConfig()
 */
class KlarnaCommunicationFactory extends AbstractCommunicationFactory
{

    /**
     * @return \SprykerEco\Zed\Klarna\Communication\Table\Payments
     */
    public function createPaymentsTable()
    {
        $paymentKlarnaQuery = $this->getQueryContainer()->queryPayments();

        return new Payments($paymentKlarnaQuery);
    }

    /**
     * @author Daniel Bohnhardt <daniel.bohnhardt@twt.de>
     *
     * @param int $idPayment
     *
     * @return \SprykerEco\Zed\Klarna\Communication\Table\StatusLog
     */
    public function createStatusLogTable($idPayment)
    {
        $statusLogQuery = $this->getQueryContainer()->queryTransactionStatusLogByPaymentId($idPayment);

        return new StatusLog($statusLogQuery, $idPayment);
    }

    /**
     * @return \SprykerEco\Zed\Klarna\Dependency\Facade\KlarnaToSalesInterface
     */
    public function getSalesFacade()
    {
        return $this->getProvidedDependency(KlarnaDependencyProvider::FACADE_SALES);
    }

}
