<?php
namespace Toppik\Report\Model\ResourceModel\Report\Subscription\Average;

use Magento\Framework\Stdlib\DateTime\DateTime;

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
        $source     = $this->objectManager->get('Toppik\OrderSource\Model\Merchant\Source');
        $periods    = $this->getConnection()->fetchCol(
                        'SELECT DISTINCT length FROM subscriptions_periods ORDER BY length ASC'
                    );
        
        foreach($source->getOptionArray() as $_key => $_label) {
            foreach($periods as $_period) {
                $_select = clone $this->getSelect();
                
                $_select
                    ->from(['main_table' => $this->_mainTable], [])
                    ->where('main_table.status != "active"')
                    ->where(new \Zend_Db_Expr(sprintf('FLOOR(main_table.frequency_length / (60 * 60 * 24)) = %s', $_period)))
                    ->where('merchant_source = ?', $_key)
                    ->columns(array(
                        'subscription_period'   => new \Zend_Db_Expr('FLOOR(main_table.frequency_length / (60 * 60 * 24))'),
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
                                                            )
                                                            ',
                                                            $_key,
                                                            $_period
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
                        'merchant_source'       => new \Zend_Db_Expr(sprintf('"%s"', $_key)),
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
        
        $this->getSelect()->union($selects)->order('subscription_period ASC');
        
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
	
	/*
		SELECT COUNT(*)
		FROM (
			SELECT frequency_length
			FROM subscriptions_profiles AS main_table
			WHERE (main_table.status = "cancelled") AND (FLOOR(main_table.frequency_length / (60 * 60 * 24)) > 0)
			GROUP BY frequency_length
		) AS m
		
		SELECT
			FLOOR(main_table.frequency_length / (60 * 60 * 24)) AS subscription_period,
			COUNT(FLOOR(main_table.frequency_length / (60 * 60 * 24))) AS count,
			MIN(
				TIMESTAMPDIFF(
					DAY,
					main_table.created_at,
					DATE_ADD(IF(ISNULL(main_table.last_order_at) || (main_table.last_order_at < main_table.created_at), main_table.created_at, main_table.last_order_at), INTERVAL main_table.frequency_length SECOND)
				)
			) AS min_value_days,
			MAX(
				TIMESTAMPDIFF(
					DAY,
					main_table.created_at,
					DATE_ADD(IF(ISNULL(main_table.last_order_at) || (main_table.last_order_at < main_table.created_at), main_table.created_at, main_table.last_order_at), INTERVAL main_table.frequency_length SECOND)
				)
			) AS max_value_days,
			FLOOR(
				AVG(
					TIMESTAMPDIFF(
						DAY,
						main_table.created_at,
						DATE_ADD(IF(ISNULL(main_table.last_order_at) || (main_table.last_order_at < main_table.created_at), main_table.created_at, main_table.last_order_at), INTERVAL main_table.frequency_length SECOND)
					)
				)
			) AS average_value_days
		FROM subscriptions_profiles AS main_table
		WHERE main_table.status = 'cancelled' AND FLOOR(main_table.frequency_length / (60 * 60 * 24)) > 0
		GROUP BY subscription_period
	*/
	
}
