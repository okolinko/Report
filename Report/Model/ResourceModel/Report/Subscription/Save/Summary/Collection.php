<?php
namespace Toppik\Report\Model\ResourceModel\Report\Subscription\Save\Summary;

class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection {
	
    /**
     * @var string
     */
    protected $_idFieldName = 'id';
    
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
    
    protected function _initSelect() {
        parent::_initSelect();
        
        $this->getSelect()->reset();
        
        $this->getSelect()
            ->from(
                ['v' => new \Zend_Db_Expr('(
                    SELECT
                        main_table.admin_id,
                        COUNT(main_table.profile_id) AS total_save,
                        COUNT(DISTINCT main_table.profile_id) AS total_save_unique,
                        GROUP_CONCAT(main_table.profile_id ORDER BY main_table.profile_id ASC SEPARATOR ", ") AS profile_ids,
                        GROUP_CONCAT(DISTINCT main_table.profile_id ORDER BY main_table.profile_id ASC SEPARATOR ", ") AS profile_ids_unique,
                        SUM(used_points) AS total_used_points,
                        SUM(admin_points) AS total_admin_points,
                        SUM(subscription_points) AS total_subscription_points,
                        GROUP_CONCAT(options.title ORDER BY options.position ASC SEPARATOR ", ") AS options_titles,
                        GROUP_CONCAT(DISTINCT options.title ORDER BY options.position ASC SEPARATOR ", ") AS options_titles_unique,
                        GROUP_CONCAT(DISTINCT options.points ORDER BY options.position ASC SEPARATOR ", ") AS options_points_unique,
                        GROUP_CONCAT(DISTINCT admin.email) AS admin_email
                    FROM subscriptions_save AS main_table
                    LEFT JOIN subscriptions_save_points AS options ON (main_table.option_id = options.id)
                    LEFT JOIN admin_user AS admin ON (main_table.admin_id = admin.user_id)
                    GROUP BY main_table.admin_id
                )')],
                []
            )
            ->columns(array('v.*'));
        
        $adminId    = 0;
        $role       = $this->authSession->getUser()->getRole();
        
        if($this->authSession->getUser() && $this->authSession->getUser()->getId()) {
            $adminId = $this->authSession->getUser()->getId();
        }
        
        if((int) $role->getId() !== 2 && !$this->_authorization->isAllowed('Toppik_Report::subscription_report_save_summary_full')) {
            $this->getSelect()
                ->where(
                    new \Zend_Db_Expr(
                        sprintf(
                            'v.admin_id = "%s"',
                            $adminId
                        )
                    )
                );
        }
        
        return $this;
    }
    
}
