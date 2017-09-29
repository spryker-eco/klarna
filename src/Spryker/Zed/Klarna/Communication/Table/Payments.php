<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Zed\Klarna\Communication\Table;

use Orm\Zed\Klarna\Persistence\Map\SpyPaymentKlarnaTableMap;
use Orm\Zed\Klarna\Persistence\SpyPaymentKlarnaQuery;
use Spryker\Shared\Url\Url;
use Spryker\Zed\Gui\Communication\Table\AbstractTable;
use Spryker\Zed\Gui\Communication\Table\TableConfiguration;

/**
 * Class Payments
 *
 * @package Spryker\Zed\Klarna\Communication\Table
 *
 * @author Daniel Bohnhardt <daniel.bohnhardt@twt.de>
 */
class Payments extends AbstractTable
{

    const FIELD_VIEW = 'FIELD_VIEW';
    const URL_KLARNA_DETAILS = '/klarna/details/';
    const PARAM_ID_PAYMENT = 'id-payment';

    /**
     * @var \Orm\Zed\Klarna\Persistence\SpyPaymentKlarnaQuery
     *
     * @author Daniel Bohnhardt <daniel.bohnhardt@twt.de>
     */
    protected $paymentKlarnaQuery;

    /**
     * @param \Orm\Zed\Klarna\Persistence\SpyPaymentKlarnaQuery $paymentKlarnaQuery
     *
     * @author Daniel Bohnhardt <daniel.bohnhardt@twt.de>
     */
    public function __construct(SpyPaymentKlarnaQuery $paymentKlarnaQuery)
    {
        $this->paymentKlarnaQuery = $paymentKlarnaQuery;
    }

    /**
     * Configure Table header.
     *
     * @param \Spryker\Zed\Gui\Communication\Table\TableConfiguration $config
     *
     * @return \Spryker\Zed\Gui\Communication\Table\TableConfiguration
     * @author Daniel Bohnhardt <daniel.bohnhardt@twt.de>
     */
    protected function configure(TableConfiguration $config)
    {
        $config->setHeader(
            [
                SpyPaymentKlarnaTableMap::COL_ID_PAYMENT_KLARNA => 'Payment ID',
                SpyPaymentKlarnaTableMap::COL_FK_SALES_ORDER    => 'Order ID',
                SpyPaymentKlarnaTableMap::COL_EMAIL             => 'Email',
                SpyPaymentKlarnaTableMap::COL_CREATED_AT        => 'Created',
                self::FIELD_VIEW                                => 'View',
            ]
        );

        $config->addRawColumn(self::FIELD_VIEW);
        $config->setSortable(
            [
                SpyPaymentKlarnaTableMap::COL_CREATED_AT,
            ]
        );

        return $config;
    }

    /**
     * @param \Spryker\Zed\Gui\Communication\Table\TableConfiguration $config
     *
     * @return array
     * @author Daniel Bohnhardt <daniel.bohnhardt@twt.de>
     */
    protected function prepareData(TableConfiguration $config)
    {
        $paymentItems = $this->runQuery($this->paymentKlarnaQuery, $config);
        $results      = [];
        foreach ($paymentItems as $paymentItem) {
            $results[] = [
                SpyPaymentKlarnaTableMap::COL_ID_PAYMENT_KLARNA => $paymentItem[SpyPaymentKlarnaTableMap::COL_ID_PAYMENT_KLARNA],
                SpyPaymentKlarnaTableMap::COL_FK_SALES_ORDER    => $paymentItem[SpyPaymentKlarnaTableMap::COL_FK_SALES_ORDER],
                SpyPaymentKlarnaTableMap::COL_EMAIL             => $paymentItem[SpyPaymentKlarnaTableMap::COL_EMAIL],
                SpyPaymentKlarnaTableMap::COL_CREATED_AT        => $paymentItem[SpyPaymentKlarnaTableMap::COL_CREATED_AT],
                self::FIELD_VIEW                                => implode(' ', $this->buildOptionsUrls($paymentItem)),
            ];
        }

        return $results;
    }

    /**
     * @param array $paymentItem
     *
     * @return array
     * @author Daniel Bohnhardt <daniel.bohnhardt@twt.de>
     */
    protected function buildOptionsUrls(array $paymentItem)
    {
        $urls = [];

        $urls[] = $this->generateViewButton(
            Url::generate(self::URL_KLARNA_DETAILS, [
                self::PARAM_ID_PAYMENT => $paymentItem[SpyPaymentKlarnaTableMap::COL_ID_PAYMENT_KLARNA],
            ]),
            'View'
        );

        return $urls;
    }

}
