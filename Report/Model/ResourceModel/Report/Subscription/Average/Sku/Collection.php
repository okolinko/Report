<?php
namespace Toppik\Report\Model\ResourceModel\Report\Subscription\Average\Sku;

class Collection extends \Magento\Reports\Model\ResourceModel\Report\Collection\AbstractCollection {
	
    /**
     * Aggregated Data Table
     *
     * @var string
     */
    protected $_mainTable = 'subscriptions_profiles';
	
    /**
     * @var ObjectManagerInterface
     */
    protected $objectManager;
	
    /**
     * @param \Magento\Framework\Data\Collection\EntityFactory $entityFactory
     * @param \Psr\Log\LoggerInterface $logger
     * @param \Magento\Framework\Data\Collection\Db\FetchStrategyInterface $fetchStrategy
     * @param \Magento\Framework\Event\ManagerInterface $eventManager
     * @param \Magento\Sales\Model\ResourceModel\Report $resource
     * @param null $connection
     */
    public function __construct(
        \Magento\Framework\ObjectManagerInterface $objectManager,
        \Magento\Framework\Data\Collection\EntityFactory $entityFactory,
        \Psr\Log\LoggerInterface $logger,
        \Magento\Framework\Data\Collection\Db\FetchStrategyInterface $fetchStrategy,
        \Magento\Framework\Event\ManagerInterface $eventManager,
        \Magento\Sales\Model\ResourceModel\Report $resource,
        \Magento\Framework\DB\Adapter\AdapterInterface $connection = null
    ) {
        $resource->init($this->_mainTable);
        parent::__construct($entityFactory, $logger, $fetchStrategy, $eventManager, $connection, $resource);
        $this->setModel('Magento\Reports\Model\Item');
        $this->objectManager = $objectManager;
    }
	
    public function initSelect() {
        $this->_reset();
        
        $selects    = array();
        $data       = $this->_getItemsData();
        
        foreach($data as $_sku => $_data) {
            $periods = (isset($_data['periods'])) ? $_data['periods'] : array();
            $sources = (isset($_data['sources'])) ? $_data['sources'] : array();
            
            foreach($periods as $_period) {
                foreach($sources as $_source) {
                    $_select = clone $this->getSelect();
                    
                    $_select
                        ->from(['main_table' => $this->_mainTable], [])
                        ->where('main_table.status != "active"')
                        ->where(new \Zend_Db_Expr(sprintf('FLOOR(main_table.frequency_length / (60 * 60 * 24)) = %s', $_period)))
                        ->where(new \Zend_Db_Expr('FLOOR(main_table.frequency_length / (60 * 60 * 24)) > 1'))
                        ->where('merchant_source = ?', $_source)
                        ->where('sku = ?', $_sku)
                        ->columns(array(
                            'sku'                   => new \Zend_Db_Expr(sprintf('"%s"', $_sku)),
                            'merchant_source'       => new \Zend_Db_Expr(sprintf('"%s"', $_source)),
                            'subscription_period'   => new \Zend_Db_Expr('FLOOR(main_table.frequency_length / (60 * 60 * 24))'),
                            'sum_subscriptions'     => new \Zend_Db_Expr('SUM(grand_total)'),
                            'count_cencelled'       => new \Zend_Db_Expr('COUNT(FLOOR(main_table.frequency_length / (60 * 60 * 24)))'),
                            'count_active'          => new \Zend_Db_Expr(
                                                            sprintf('
                                                                (
                                                                    SELECT COUNT(profile_id)
                                                                    FROM subscriptions_profiles
                                                                    WHERE
                                                                        status = "active"
                                                                        AND merchant_source = "%s"
                                                                        AND (FLOOR(frequency_length / (60 * 60 * 24)) = %s)
                                                                        AND sku = "%s"
                                                                )
                                                                ',
                                                                $_source,
                                                                $_period,
                                                                $_sku
                                                            )
                                                    ),
                            'count_orders'          => new \Zend_Db_Expr('
                                FLOOR(
                                    AVG(
                                        TIMESTAMPDIFF(
                                            DAY,
                                            main_table.created_at,
                                            DATE_ADD(IF(ISNULL(main_table.last_order_at) || (main_table.last_order_at < main_table.created_at), main_table.created_at, main_table.last_order_at), INTERVAL main_table.frequency_length SECOND)
                                        )
                                    )
                                ) / FLOOR(main_table.frequency_length / (60 * 60 * 24))
                            '),
                            'min_value_days'        => new \Zend_Db_Expr('
                                MIN(
                                    TIMESTAMPDIFF(
                                        DAY,
                                        main_table.created_at,
                                        DATE_ADD(IF(ISNULL(main_table.last_order_at) || (main_table.last_order_at < main_table.created_at), main_table.created_at, main_table.last_order_at), INTERVAL main_table.frequency_length SECOND)
                                    )
                                )
                            '),
                            'max_value_days'        => new \Zend_Db_Expr('
                                MAX(
                                    TIMESTAMPDIFF(
                                        DAY,
                                        main_table.created_at,
                                        DATE_ADD(IF(ISNULL(main_table.last_order_at) || (main_table.last_order_at < main_table.created_at), main_table.created_at, main_table.last_order_at), INTERVAL main_table.frequency_length SECOND)
                                    )
                                )
                            '),
                            'average_value_days'    => new \Zend_Db_Expr('
                                FLOOR(
                                    AVG(
                                        TIMESTAMPDIFF(
                                            DAY,
                                            main_table.created_at,
                                            DATE_ADD(IF(ISNULL(main_table.last_order_at) || (main_table.last_order_at < main_table.created_at), main_table.created_at, main_table.last_order_at), INTERVAL main_table.frequency_length SECOND)
                                        )
                                    )
                                )
                            ')
                        ))
                        ->group('subscription_period');
                    
                    $selects[] = $_select;
                }
            }
        }
        
        $this->getSelect()->union($selects)->order(new \Zend_Db_Expr('subscription_period ASC, merchant_source ASC'));
        
        return $this;
    }
	
    /**
     * Get SQL for get record count
     *
     * @return Select
     */
    public function getSelectCountSql() {
        $countSelect = clone $this->getSelect();
		
		$countSelect->reset();
		
        $countSelect
            ->from(['m' => new \Zend_Db_Expr('(
				SELECT frequency_length
				FROM subscriptions_profiles AS main_table
				WHERE (main_table.status != "active") AND (FLOOR(main_table.frequency_length / (60 * 60 * 24)) > 0)
				GROUP BY CONCAT(frequency_length, merchant_source)
			)')], [])
            ->columns(array(
				new \Zend_Db_Expr('COUNT(*)')
            ));
		
		return $countSelect;
    }
	
    protected function _getItemsData() {
        $collection = array();
        
        $data       = $this->getConnection()->fetchAll(
                        '
                            SELECT
                                sku,
                                GROUP_CONCAT(DISTINCT FLOOR(frequency_length / (60 * 60 * 24)) ORDER BY frequency_length ASC) AS periods,
                                GROUP_CONCAT(DISTINCT merchant_source ORDER BY merchant_source ASC) AS sources
                            FROM subscriptions_profiles
                            GROUP BY sku
                            ORDER BY sku ASC'
                    );
         
         foreach($data as $_item) {
             $periods = (isset($_item['periods'])) ? explode(',', $_item['periods']) : array();
             $sources = (isset($_item['sources'])) ? explode(',', $_item['sources']) : array();
             
             $collection[$_item['sku']] = array('periods' => $periods, 'sources' => $sources);
         }
         
         return $collection;
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
    
}
