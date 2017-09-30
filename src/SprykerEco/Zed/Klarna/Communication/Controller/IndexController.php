<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace SprykerEco\Zed\Klarna\Communication\Controller;

use Spryker\Zed\Application\Communication\Controller\AbstractController;

/**
 * Class IndexController
 *
 * @package SprykerEco\Zed\Klarna\Communication\Controller
 *
 * @author Daniel Bohnhardt <daniel.bohnhardt@twt.de>
 *
 * @method \SprykerEco\Zed\Klarna\Communication\KlarnaCommunicationFactory getFactory()
 */
class IndexController extends AbstractController
{

    /**
     * @return array
     */
    public function indexAction()
    {
        $table = $this->getFactory()->createPaymentsTable();

        return [
            'payments' => $table->render(),
        ];
    }

    /**
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     */
    public function tableAction()
    {
        $table = $this->getFactory()->createPaymentsTable();

        return $this->jsonResponse($table->fetchData());
    }

}
