<?php
namespace Toppik\Report\Model\ResourceModel\Report\Subscription\Save;

class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection {
	
    /**
     * @var string
     */
    protected $_idFieldName = 'id';
    
    protected $_fieldMap = [
        'store_id' => 'p.store_id',
        'created_at' => 'main_table.created_at',
        'id' => 'main_table.id',
        'option_title' => 'options.title',
        'option_points' => 'options.points',
        'admin_email' => 'admin.email'
    ];
    
    /**
     * @var \Magento\Backend\Model\Auth\Session
     */
    protected $authSession;
    
    /**
     * @var \Magento\Framework\AuthorizationInterface
     */
    protected $_authorization;
    
    public function __construct(
        \Magento\Framework\Data\Collection\EntityFactoryInterface $entityFactory,
        \Psr\Log\LoggerInterface $logger,
        \Magento\Framework\Data\Collection\Db\FetchStrategyInterface $fetchStrategy,
        \Magento\Framework\Event\ManagerInterface $eventManager,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Backend\Model\Auth\Session $authSession,
        \Magento\Framework\AuthorizationInterface $authorization,
        \Magento\Framework\DB\Adapter\AdapterInterface $connection = null,
        \Magento\Framework\Model\ResourceModel\Db\AbstractDb $resource = null
    ) {
        $this->storeManager = $storeManager;
        $this->authSession = $authSession;
        $this->_authorization = $authorization;
        
        $this->_init(
            'Toppik\Subscriptions\Model\Profile\Save',
            'Toppik\Subscriptions\Model\ResourceModel\Profile\Save'
        );
        
        parent::__construct($entityFactory, $logger, $fetchStrategy, $eventManager, $connection, $resource);
    }
    
    public function addFieldToFilter($field, $condition = null) {
        if(isset($this->_fieldMap[$field])) {
            $field = $this->_fieldMap[$field];
        }
        
        return parent::addFieldToFilter($field, $condition);
    }
    
    protected function _initSelect() {
        parent::_initSelect();
        
        $this->getSelect()
            ->joinLeft(
                ['p' => $this->getTable('subscriptions_profiles')],
                'main_table.profile_id = p.profile_id',
                [
                    'p.customer_id',
                    'p.sku',
                    'p.store_id'
                ]
            )
            ->joinLeft(
                ['options' => \Toppik\Subscriptions\Model\ResourceModel\Profile\Points::MAIN_TABLE],
                "(main_table.option_id = options.id)",
                [
                    'options.title AS option_title',
                    'options.points AS option_points'
                ]
            )
            ->joinLeft(
                ['admin' => 'admin_user'],
                "(main_table.admin_id = admin.user_id)",
                [
                    'admin.email AS admin_email'
                ]
            )
            ->columns([
                'ip_converted' => 'INET_NTOA(main_table.ip)'
            ]);
        
        $adminId    = 0;
        $role       = $this->authSession->getUser()->getRole();
        
        if($this->authSession->getUser() && $this->authSession->getUser()->getId()) {
            $adminId = $this->authSession->getUser()->getId();
        }
        
        if((int) $role->getId() !== 2 && !$this->_authorization->isAllowed('Toppik_Report::subscription_report_save_full')) {
            $this->getSelect()
                ->where(
                    new \Zend_Db_Expr(
                        sprintf(
                            'main_table.admin_id = "%s"',
                            $adminId
                        )
                    )
                );
        }
        
        return $this;
    }
    
}
