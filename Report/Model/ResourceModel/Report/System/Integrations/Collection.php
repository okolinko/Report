<?php
namespace Toppik\Report\Model\ResourceModel\Report\System\Integrations;

class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection {
	
    /**
     * @var string
     */
    protected $_idFieldName = 'id';
    
    /**
     * @var \Magento\Backend\Model\Auth\Session
     */
    protected $authSession;
    
    public function __construct(
        \Magento\Framework\Data\Collection\EntityFactoryInterface $entityFactory,
        \Psr\Log\LoggerInterface $logger,
        \Magento\Framework\Data\Collection\Db\FetchStrategyInterface $fetchStrategy,
        \Magento\Framework\Event\ManagerInterface $eventManager,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Backend\Model\Auth\Session $authSession,
        \Magento\Framework\DB\Adapter\AdapterInterface $connection = null,
        \Magento\Framework\Model\ResourceModel\Db\AbstractDb $resource = null
    ) {
        $this->storeManager = $storeManager;
        $this->authSession = $authSession;
        
        $this->_init(
            'Toppik\Report\Model\System\Integrations',
            'Toppik\Report\Model\ResourceModel\Report\System\Integrations'
        );
        
        parent::__construct($entityFactory, $logger, $fetchStrategy, $eventManager, $connection, $resource);
    }
    
    protected function _initSelect() {
        parent::_initSelect();
        
        $adminId = 0;
        
        if($this->authSession->getUser() && $this->authSession->getUser()->getId()) {
            $adminId = $this->authSession->getUser()->getId();
        }
        
        $this->getSelect()
            ->where(
                new \Zend_Db_Expr(
                    sprintf(
                        'main_table.entity_type IN (SELECT entity_type_code FROM %s WHERE FIND_IN_SET(%s, admin_ids))',
                        $this->getTable('toppikreport_daily_system_entity_type'),
                        $adminId
                    )
                )
            );
        
        return $this;
    }
    
}
