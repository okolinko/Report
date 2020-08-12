<?php
namespace Toppik\Report\Model\ResourceModel\Report\Subscription\Daily;

use Magento\Framework\Stdlib\DateTime\DateTime;

class Collection extends \Magento\Reports\Model\ResourceModel\Report\Collection\AbstractCollection {
	
    /**
     * Aggregated Data Table
     *
     * @var string
     */
    protected $_mainTable = 'subscription_report_daily';
	
    /**
     * @var \Magento\Framework\Stdlib\DateTime\TimezoneInterface
     */
    private $tz;
    
    /**
     * @param \Magento\Framework\Data\Collection\EntityFactory $entityFactory
     * @param \Psr\Log\LoggerInterface $logger
     * @param \Magento\Framework\Data\Collection\Db\FetchStrategyInterface $fetchStrategy
     * @param \Magento\Framework\Event\ManagerInterface $eventManager
     * @param \Magento\Sales\Model\ResourceModel\Report $resource
     * @param null $connection
     */
    public function __construct(
        \Magento\Framework\Stdlib\DateTime\TimezoneInterface $tz,
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
        $this->tz = $tz;
    }
    
    public function initSelect() {
        $this->_reset();
        
        $this->getSelect()
            ->from(['main_table' => $this->_mainTable])
            ->order('date ASC');
        
        return parent::_beforeLoad();
    }
    
    public function setDateRange($from = NULL, $to = NULL) {
        $this->initSelect();
        
        $date = new \DateTime($from);
        $from = $date->format('Y-m-d');
        
        $date = new \DateTime($to);
        $to = $date->format('Y-m-d');
        
        $this->getSelect()->where("date BETWEEN '" . $from . "' AND '" . $to . "'");
        
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
     * Apply stores filter to select object
     *
     * @param \Magento\Framework\DB\Select $select
     * @return $this
     */
    public function addOrderStatusFilter() {
        return $this;
    }
    
}
