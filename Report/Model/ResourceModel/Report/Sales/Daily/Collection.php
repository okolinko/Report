<?php
namespace Toppik\Report\Model\ResourceModel\Report\Sales\Daily;

use Magento\Framework\Stdlib\DateTime\DateTime;

class Collection extends \Toppik\Report\Model\ResourceModel\Report\Collection
{

    /**
     * Aggregated Data Table
     *
     * @var string
     */
    protected $_mainTable = 'sales_order';

    /**
     * Grouping
     *
     * @var string
     */
    protected $_grouping = null;

    /**
     * Customer Group
     *
     * @var string
     */
    protected $_customerGroup = null;

    /**
     * Customer Groups
     *
     * @var \Toppik\Report\Model\Sales\Source\CustomerGroupsFactory
     */
    protected $_customerGroupsFactory;

    /**
     * @param \Magento\Framework\Stdlib\DateTime\TimezoneInterface $tz
     * @param \Toppik\Report\Model\Sales\Source\CustomerGroupsFactory $customerGroupsFactory
     * @param \Magento\Framework\Data\Collection\EntityFactory $entityFactory
     * @param \Psr\Log\LoggerInterface $logger
     * @param \Magento\Framework\Data\Collection\Db\FetchStrategyInterface $fetchStrategy
     * @param \Magento\Framework\Event\ManagerInterface $eventManager
     * @param \Magento\Sales\Model\ResourceModel\Report $resource
     * @param \Magento\Framework\DB\Adapter\AdapterInterface $connection
     */
    public function __construct(
        \Magento\Framework\Stdlib\DateTime\TimezoneInterface $tz,
        \Toppik\Report\Model\Sales\Source\CustomerGroupsFactory $customerGroupsFactory,
        \Magento\Framework\Data\Collection\EntityFactory $entityFactory,
        \Psr\Log\LoggerInterface $logger,
        \Magento\Framework\Data\Collection\Db\FetchStrategyInterface $fetchStrategy,
        \Magento\Framework\Event\ManagerInterface $eventManager,
        \Magento\Sales\Model\ResourceModel\Report $resource,
        \Magento\Framework\DB\Adapter\AdapterInterface $connection = null
    )
    {
        $resource->init($this->_mainTable);
        $this->_customerGroupsFactory = $customerGroupsFactory;
        parent::__construct($tz, $entityFactory, $logger, $fetchStrategy, $eventManager, $resource, $connection);
    }

    public function initSelect() {
        $this->_reset();

        $_select = $this->getSelect();

        $_select
            ->from(['main_table' => $this->_mainTable])
            ->joinInner(
                array(
                    'units' =>  new \Zend_Db_Expr(
                        '(select sum(sales_order_item.qty_ordered) as qty, sales_order_item.order_id
                        FROM sales_order_item
                        group by order_id
                    )'
                    ),
                ),
                'units.order_id = main_table.entity_id'
            )
            ->joinInner(
                array(
                    'sh' =>  new \Zend_Db_Expr(
                                '(SELECT sh.order_id, sh.created_at
            FROM sales_shipment sh
            WHERE
                entity_id IN (SELECT
                        MIN(entity_id)
                    FROM
                        sales_shipment
                    GROUP BY order_id))'
                    ),
                ),
                'sh.order_id = main_table.entity_id'
            )
            ->joinLeft(array(
                'mod'    =>  $this->getTable('microsite_order_detail')),
                'mod.order_id = main_table.entity_id',
                array()
            )
            ->reset(\Zend_DB_Select::COLUMNS)
            ->columns(array(
                'entity_id' => 'main_table.entity_id',
                'source' => 'main_table.source',
                'merchant_source' => 'main_table.merchant_source',
                'grand_total' => new \Zend_Db_Expr('SUM(main_table.grand_total)'),
                'subtotal' => new \Zend_Db_Expr('SUM(main_table.subtotal)'),
                'shipping' => new \Zend_Db_Expr('SUM(coalesce(main_table.shipping_amount,0))'),
                'taxes' => new \Zend_Db_Expr('SUM(coalesce(main_table.tax_amount,0))'),
                'discount' => new \Zend_Db_Expr('SUM(coalesce(main_table.discount_amount,0))'),
                'refund' => new \Zend_Db_Expr('SUM(coalesce(main_table.total_refunded,0))'),
                'order_volume' => new \Zend_Db_Expr('count(*)'),
                'created_at_min' => new \Zend_Db_Expr('MIN(sh.created_at)'),
                'created_at_max' => new \Zend_Db_Expr('MAX(sh.created_at)'),
                'total_unit_sales' => new \Zend_Db_Expr('SUM(units.qty)'),
                'mod.project_code AS project_code',
                'mod.media_code AS media_code',
                'mod.campaign_description AS campaign_description'

            ))
            ->where('main_table.status IN(?)', ['complete' , 'closed'])
            ->group('date_range')
            ->group('source')
            ->group('merchant_source')
            ->order('merchant_source')
            ->order('source ASC')
            ->order('date_range DESC');
        ;

        return $this;
    }

    /**
     * @param int $groupId
     * @return $this
     */
    public function setCustomerGroups($groupId = 0)
    {
        $customerGroups = $this->_customerGroupsFactory->create();

        $customerGroupsValue = $customerGroups->getCustomeGroupById($groupId);

        if (is_array($customerGroupsValue)) {
            $this->getSelect()->where('main_table.customer_group_id IN (?)', $customerGroupsValue);
        }

        return $this;
    }

    public function setGrouping($grouping = null)
    {

        $convertDate = ' convert_tz(sh.created_at,\'GMT\',\'' . $this->tz->getConfigTimezone() . '\' ) ';


        switch($grouping) {
            case 'daily':
                $format = '%m-%d-%Y';
                $expr = new \Zend_Db_Expr('DATE_FORMAT( ' . $convertDate . ' , \'' . $format . '\')');
                break;
            case 'monthly':
                $format = '%m-%Y';
                $expr = new \Zend_Db_Expr('DATE_FORMAT( ' . $convertDate . ' + INTERVAL 1 DAY , \'' . $format . '\')');
                break;
            case 'quarterly':
                $format = '%m-%Y';

                $expr = new \Zend_Db_Expr('Concat("Q", Quarter(' . $convertDate . ' + INTERVAL 1 DAY), " ", Year(' . $convertDate . '))');

                break;
            case 'yearly':
                $format = '%Y';
                $expr = new \Zend_Db_Expr('DATE_FORMAT( ' . $convertDate . ' , \'' . $format . '\')');
                break;
            default:
                $format = '%m-%Y';
                $expr = new \Zend_Db_Expr('DATE_FORMAT( ' . $convertDate . ' + INTERVAL 1 DAY , \'' . $format . '\')');
                break;

        }


        $this->getSelect()->columns([
            'date_range' => $expr
        ]);

#var_dump($this->getSelect().'');

        return $this;
    }
    
    public function setDateRange($from = NULL, $to = NULL) {
        $this->initSelect();
        
        $canFixDate = true;
        
        $date = new \DateTime($from, new \DateTimeZone($this->tz->getConfigTimezone()));
        $date->setTimezone(new \DateTimeZone($this->tz->getDefaultTimezone()));
        
        if($canFixDate) {
            $date->sub(new \DateInterval("P1D"));
        }
        
        $from = $date->format('Y-m-d H:i:s');

        $date = new \DateTime($to, new \DateTimeZone($this->tz->getConfigTimezone()));
        $date->setTimezone(new \DateTimeZone($this->tz->getDefaultTimezone()));
        
        if($canFixDate) {
            $date->sub(new \DateInterval("P1D"));
        }
        
        $to = $date->format('Y-m-d H:i:s');

        $this->getSelect()->where("sh.created_at BETWEEN '" . $from . "' AND '" . $to . "'");

        return $this;
    }
    
    /**
     * Apply needed aggregated table
     *
     * @return $this
     */
    protected function _applyAggregatedTable() {
        return $this;
    }


    /**
     * @return $this
     */
    public function makeTotalSelect()
    {
        if ($this->isTotals()) {
            $this->_selectedColumns = $this->getAggregatedColumns();


            $totalSelectString = $this->getSelect(). '';
            $select = $this->getSelect();
            $select->reset();

            $select->from(['main_table' => new \Zend_Db_Expr('(' . $totalSelectString . ')')], []);

            $select->columns(
                $this->getAggregatedColumns()
            );

        }

        return $this;
    }


    /**
     * Apply date range filter
     *
     * @return $this
     */
    protected function _applyDateRangeFilter() {
        return $this;
    }

    /**
     * Apply stores filter to select object
     *
     * @param \Magento\Framework\DB\Select $select
     * @return $this
     */
    protected function _applyStoresFilterToSelect(\Magento\Framework\DB\Select $select) {
        return $this;
    }

    /**
     * Apply stores filter
     *
     * @return $this
     */
    protected function _applyStoresFilter() {
        return $this;
    }

    /**
     * Custom filters application ability
     *
     * @return $this
     */
    protected function _applyCustomFilter() {
        return $this;
    }

    /**
     * Get SQL for get record count
     *
     * @return Select
     */
    public function getSelectCountSql() {
        $this->_renderFilters();

        $countSelect = clone $this->getSelect();
        $countSelect->reset();

        $countSelect
            ->from(['m' => new \Zend_Db_Expr('(' .
                $this->getSelect()
                    ->reset(\Magento\Framework\DB\Select::LIMIT_COUNT)
                    ->reset(\Magento\Framework\DB\Select::LIMIT_OFFSET)
                . ')')], []);

        $countSelect->columns(new \Zend_Db_Expr('COUNT(*)'));
        return $countSelect;
    }
}
