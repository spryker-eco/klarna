<?php

/**
 * MIT License
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace SprykerEco\Zed\Klarna\Communication\Table;

use Orm\Zed\Klarna\Persistence\Map\SpyPaymentKlarnaTransactionStatusLogTableMap;
use Orm\Zed\Klarna\Persistence\SpyPaymentKlarnaTransactionStatusLogQuery;
use Spryker\Zed\Gui\Communication\Table\AbstractTable;
use Spryker\Zed\Gui\Communication\Table\TableConfiguration;

/**
 * Class StatusLog
 *
 * @package SprykerEco\Zed\Klarna\Communication\Table
 *
 * @author Daniel Bohnhardt <daniel.bohnhardt@twt.de>
 */
class StatusLog extends AbstractTable
{
    const FIELD_DETAILS = 'FIELD_DETAILS';

    /**
     * @var int
     *
     * @author Daniel Bohnhardt <daniel.bohnhardt@twt.de>
     */
    protected $idPayment;

    /**
     * @var \Orm\Zed\Klarna\Persistence\SpyPaymentKlarnaTransactionStatusLogQuery
     *
     * @author Daniel Bohnhardt <daniel.bohnhardt@twt.de>
     */
    protected $statusLogQuery;

    /**
     * @var string[]
     */
    private static $includeFields = [
        SpyPaymentKlarnaTransactionStatusLogTableMap::COL_CREATED_AT,
        SpyPaymentKlarnaTransactionStatusLogTableMap::COL_PROCESSING_STATUS,
        SpyPaymentKlarnaTransactionStatusLogTableMap::COL_PROCESSING_TYPE,
    ];

    /**
     * @param \Orm\Zed\Klarna\Persistence\SpyPaymentKlarnaTransactionStatusLogQuery $statusLogQuery
     * @param int $idPayment
     */
    public function __construct(SpyPaymentKlarnaTransactionStatusLogQuery $statusLogQuery, $idPayment)
    {
        $this->statusLogQuery = $statusLogQuery;
        $this->idPayment = $idPayment;
    }

    /**
     * @param \Spryker\Zed\Gui\Communication\Table\TableConfiguration $config
     *
     * @return \Spryker\Zed\Gui\Communication\Table\TableConfiguration
     */
    protected function configure(TableConfiguration $config)
    {
        $config->setHeader($this->getHeaderFields());
        $config->addRawColumn(self::FIELD_DETAILS);
        $config->setUrl('status-log-table?id-payment=' . $this->idPayment);

        return $config;
    }

    /**
     * @return array
     */
    private function getHeaderFields()
    {
        $headerFields = [];
        foreach (self::$includeFields as $fieldName) {
            $translatedFieldName = SpyPaymentKlarnaTransactionStatusLogTableMap::translateFieldName(
                $fieldName,
                SpyPaymentKlarnaTransactionStatusLogTableMap::TYPE_COLNAME,
                SpyPaymentKlarnaTransactionStatusLogTableMap::TYPE_FIELDNAME
            );

            $fieldLabel = str_replace(['processing_', 'identification_'], '', $translatedFieldName);
            $headerFields[$translatedFieldName] = $fieldLabel;
        }

        $headerFields[self::FIELD_DETAILS] = 'Details';

        return $headerFields;
    }

    /**
     * @param \Spryker\Zed\Gui\Communication\Table\TableConfiguration $config
     *
     * @return array
     */
    protected function prepareData(TableConfiguration $config)
    {
        $logItems = $this->runQuery($this->statusLogQuery, $config);
        $results = [];
        foreach ($logItems as $logItem) {
            $results[] = $this->getFieldMatchedResultArrayFromLogItem($logItem);
        }

        return $results;
    }

    /**
     * Returns an array that matches field values from $logItem with the table's
     * fields so that it renders correctly assigned field.
     *
     * @param array $logItem
     *
     * @return array
     */
    private function getFieldMatchedResultArrayFromLogItem(array $logItem)
    {
        $resultArray = [];
        foreach (self::$includeFields as $fieldName) {
            $translatedFieldName = SpyPaymentKlarnaTransactionStatusLogTableMap::translateFieldName(
                $fieldName,
                SpyPaymentKlarnaTransactionStatusLogTableMap::TYPE_COLNAME,
                SpyPaymentKlarnaTransactionStatusLogTableMap::TYPE_FIELDNAME
            );

            $resultArray[$translatedFieldName] = $logItem[$fieldName];
        }

        $resultArray[self::FIELD_DETAILS] = $this->getDetailsFieldValue($logItem);

        return $resultArray;
    }

    /**
     * Dumps all remaining fields (and their values) into a single string representation.
     *
     * @param array $logItem
     *
     * @return string
     */
    private function getDetailsFieldValue(array $logItem)
    {
        $fieldNames = SpyPaymentKlarnaTransactionStatusLogTableMap::getFieldNames(
            SpyPaymentKlarnaTransactionStatusLogTableMap::TYPE_COLNAME
        );
        $tupleRows = [];
        foreach ($fieldNames as $fieldName) {
            if (in_array($fieldName, self::$includeFields)) {
                continue;
            }

            $translatedFieldName = SpyPaymentKlarnaTransactionStatusLogTableMap::translateFieldName(
                $fieldName,
                SpyPaymentKlarnaTransactionStatusLogTableMap::TYPE_COLNAME,
                SpyPaymentKlarnaTransactionStatusLogTableMap::TYPE_FIELDNAME
            );

            $tupleRows[] = sprintf('%s:&nbsp;%s', $translatedFieldName, $logItem[$fieldName]);
        }

        $detailsText = implode('<br />', $tupleRows);

        return $detailsText;
    }
}
