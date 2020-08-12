<?php
namespace Toppik\Report\Model\ResourceModel\Report\Subscription\Index;

use Magento\Framework\Stdlib\DateTime\DateTime;

class Collection extends \Magento\Reports\Model\ResourceModel\Report\Collection\AbstractCollection {
	
    /**
     * Aggregated Data Table
     *
     * @var string
     */
    protected $_mainTable = 'subscriptions_profiles';
	
    /**
     * @param \Magento\Framework\Data\Collection\EntityFactory $entityFactory
     * @param \Psr\Log\LoggerInterface $logger
     * @param \Magento\Framework\Data\Collection\Db\FetchStrategyInterface $fetchStrategy
     * @param \Magento\Framework\Event\ManagerInterface $eventManager
     * @param \Magento\Sales\Model\ResourceModel\Report $resource
     * @param null $connection
     */
    public function __construct(
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
    }
    
    public function initSelect() {
        $this->_reset();
        
        $objectManager  = \Magento\Framework\App\ObjectManager::getInstance();
        
        $helper         = $objectManager->get('Magento\Backend\Helper\Data');
        $request        = $objectManager->get('Magento\Framework\App\Request\Http');
        $dateTimeFilter = $objectManager->get('Magento\Framework\Stdlib\DateTime\Filter\DateTime');
        $profile        = $objectManager->get('Toppik\Subscriptions\Model\Profile');
        $source         = $objectManager->get('Toppik\OrderSource\Model\Merchant\Source');
        
        $filterParams = $helper->prepareFilterString(
            $request->getParam('filter')
        );
        
        $inputFilter = new \Zend_Filter_Input(
            ['from' => $dateTimeFilter, 'to' => $dateTimeFilter],
            [],
            $filterParams
        );
        
        $requestData    = $inputFilter->getUnescaped();
        $from           = isset($requestData['from']) ? $requestData['from'] : null;
        $to             = isset($requestData['to']) ? $requestData['to'] : null;
        
        if($from === null && $to === null) {
            return $this;
        }
        
        $from           = new \DateTime($from);
        $to             = new \DateTime($to);
        
        $to->add(new \DateInterval("PT23H59M59S"));
        
        $period_from    = $from->format('Y-m-d H:i:s');
        $period_to      = $to->format('Y-m-d H:i:s');
        
        $from->add(new \DateInterval("PT7H"));
        $to->add(new \DateInterval("PT7H"));
        
        $from_start     = $from->format('Y-m-d H:i:s');
        $to_start       = $to->format('Y-m-d H:i:s');
        
        $from->add(new \DateInterval("PT23H59M59S"));
        
        $from_end       = $from->format('Y-m-d H:i:s');
        $to_end         = $to->format('Y-m-d H:i:s');
        
        $main_source    = \Toppik\OrderSource\Model\Merchant\Source::SOURCE_0;
        
        $columns        = array(
            'period_from_original'  => new \Zend_Db_Expr(sprintf('"%s"', $period_from)),
            'period_to_original'    => new \Zend_Db_Expr(sprintf('"%s"', $period_to)),
            'period_from'           => new \Zend_Db_Expr(sprintf('"%s"', $from_start)),
            'period_to'             => new \Zend_Db_Expr(sprintf('"%s"', $to_end)),
            'count_orders'          => new \Zend_Db_Expr(
                sprintf(
                    '
                        (
                            SELECT COUNT(o.entity_id)
                            FROM subscriptions_profiles_orders AS so
                            INNER JOIN sales_order AS o ON o.entity_id = so.order_id
                            WHERE o.created_at BETWEEN "%s" AND "%s"
                        )
                    ',
                    $from_start,
                    $to_end
                )
            ),
            'sum_orders'            => new \Zend_Db_Expr(
                sprintf(
                    '
                        (
                            SELECT SUM(o.grand_total)
                            FROM subscriptions_profiles_orders AS so
                            INNER JOIN sales_order AS o ON o.entity_id = so.order_id
                            WHERE o.created_at BETWEEN "%s" AND "%s"
                        )
                    ',
                    $from_start,
                    $to_end
                )
            )
        );
        
        $statuses = array(
            \Toppik\Subscriptions\Model\Profile::STATUS_ACTIVE => __('Active'),
            \Toppik\Subscriptions\Model\Profile::STATUS_SUSPENDED => __('Suspended'),
            \Toppik\Subscriptions\Model\Profile::STATUS_CANCELLED => __('Cancelled')
        );
        
        foreach(array('from', 'to') as $_t) {
            foreach($statuses as $_status => $_status_label) {
                foreach($source->getOptionArray() as $_s_key => $_source_label) {
                    foreach(array('count', 'sum') as $_ctype) {
                        $columns = array_merge(
                                        $columns,
                                        array(
                                            sprintf('%s_%s_%s_%s', $_status, (($_s_key == $main_source) ? 't' : 'ms'), $_ctype, $_t) => new \Zend_Db_Expr(
                                                sprintf(
                                                    '
                                                        (
                                                            SELECT %s(%s)
                                                            FROM subscriptions_profiles
                                                            WHERE
                                                                (%s BETWEEN "%s" AND "%s") AND status = "%s" AND merchant_source %s "%s"
                                                            GROUP BY status
                                                        )
                                                    ',
                                                    strtoupper($_ctype),
                                                    (($_ctype == 'count') ? 'profile_id' : 'grand_total'),
                                                    (
                                                        (
                                                            $_status == \Toppik\Subscriptions\Model\Profile::STATUS_ACTIVE
                                                        ) ?
                                                            'created_at'
                                                            :
                                                            sprintf('IF(ISNULL(%s_at), updated_at, %s_at)', $_status, $_status)
                                                    ),
                                                    eval(sprintf('return $%s_start;', $_t)),
                                                    eval(sprintf('return $%s_end;', $_t)),
                                                    $_status,
                                                    (($_s_key == $main_source) ? '=' : '!='),
                                                    $main_source
                                                )
                                            )
                                        )
                                   );
                    }
                }
            }
        }
        
        $this->getSelect()
            ->reset()
            ->from(['main_table' => $this->_mainTable], [])
            ->columns($columns)
            ->reset(\Zend_Db_Select::FROM);
        
		return $this;
    }
    
    public function getSize() {
        return 1;
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
