<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Zed\Klarna\Communication\Controller;

use Spryker\Zed\Application\Communication\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * Class DetailsController
 *
 * @package Spryker\Zed\Klarna\Communication\Controller
 *
 * @author Daniel Bohnhardt <daniel.bohnhardt@twt.de>
 *
 * @method \Spryker\Zed\Klarna\Communication\KlarnaCommunicationFactory getFactory()
 * @method \Spryker\Zed\Klarna\Persistence\KlarnaQueryContainerInterface getQueryContainer()
 */
class DetailsController extends AbstractController
{

    /**
     * @param \Symfony\Component\HttpFoundation\Request $request
     *
     * @return array
     */
    public function indexAction(Request $request)
    {
        $idPayment      = (int)$request->get('id-payment');
        $paymentEntity  = $this->getPaymentEntity($idPayment);
        $statusLogTable = $this->getFactory()->createStatusLogTable($idPayment);

        return [
            'idPayment'      => $idPayment,
            'paymentDetails' => $paymentEntity,
            'statusLogTable' => $statusLogTable->render(),
        ];
    }

    /**
     * @param int $idPayment
     *
     * @return \Orm\Zed\Klarna\Persistence\SpyPaymentKlarna
     * @throws \Symfony\Component\HttpKernel\Exception\NotFoundHttpException
     * @author Daniel Bohnhardt <daniel.bohnhardt@twt.de>
     */
    private function getPaymentEntity($idPayment)
    {
        $paymentEntity = $this->getQueryContainer()->queryPaymentById($idPayment)->findOne();

        if ($paymentEntity === null) {
            throw new NotFoundHttpException('Payment entity could not be found');
        }

        return $paymentEntity;
    }

    /**
     * @param \Symfony\Component\HttpFoundation\Request $request
     *
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     */
    public function statusLogTableAction(Request $request)
    {
        $idPayment      = (int)$request->get('id-payment');
        $statusLogTable = $this->getFactory()->createStatusLogTable($idPayment);

        return $this->jsonResponse($statusLogTable->fetchData());
    }

}
