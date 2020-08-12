<?php
namespace Toppik\Report\Model\ResourceModel\Report\Subscription\Monthly;

use Magento\Framework\Stdlib\DateTime\DateTime;

class Collection extends \Magento\Reports\Model\ResourceModel\Report\Collection\AbstractCollection {
	
    /**
     * Aggregated Data Table
     *
     * @var string
     */
    protected $_mainTable = 'subscriptions_report';
	
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
		
        $this->getSelect()
            ->from(['main_table' => $this->_mainTable])
            ->order('id ASC');
		
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
