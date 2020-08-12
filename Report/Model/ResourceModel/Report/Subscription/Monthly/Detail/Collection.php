<?php
namespace Toppik\Report\Model\ResourceModel\Report\Subscription\Monthly\Detail;

class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection {
	
    /**
     * @var string
     */
    protected $_idFieldName = 'id';
    
    public function __construct(
        \Magento\Framework\Data\Collection\EntityFactoryInterface $entityFactory,
        \Psr\Log\LoggerInterface $logger,
        \Magento\Framework\Data\Collection\Db\FetchStrategyInterface $fetchStrategy,
        \Magento\Framework\Event\ManagerInterface $eventManager,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Framework\DB\Adapter\AdapterInterface $connection = null,
        \Magento\Framework\Model\ResourceModel\Db\AbstractDb $resource = null
    ) {
        $this->_init(
            'Toppik\Report\Model\Report\Subscription\Monthly',
            'Toppik\Report\Model\ResourceModel\Report\Subscription\Monthly'
        );
        
        parent::__construct($entityFactory, $logger, $fetchStrategy, $eventManager, $connection, $resource);
        
        $this->storeManager = $storeManager;
    }
    
    protected function _initSelect() {
        parent::_initSelect();
        
        $this->getSelect()
            ->joinLeft(
                ['orders' => 'sales_order'],
                "(main_table.entity_id = orders.entity_id)",
                [
                    'orders.entity_id AS order_real_id',
                    'orders.source as source',
                    'orders.merchant_source as merchant_source',
                    'orders.coupon_code as coupon_code',
                    'orders.admin_id as admin_id'
                ]
            )
            ->joinLeft(
                ['payment' => 'sales_order_payment'],
                "(main_table.entity_id = payment.parent_id)",
                [
                   'payment.cc_last_4 as credit_card'
                ]
            )
            ->joinLeft(
                ['adminuser' => 'admin_user'],
                "(orders.admin_id = adminuser.user_id)",
                [
                   'CONCAT_WS(" ", adminuser.firstname, adminuser.lastname) AS admin_username'
                ]
            )
            ->joinLeft(
                ['customer' => 'customer_entity'],
                "(main_table.customer_id = customer.entity_id)",
                [
                    'customer.email AS customer_email',
                ]
            );
        
        return $this;
    }
    
}
