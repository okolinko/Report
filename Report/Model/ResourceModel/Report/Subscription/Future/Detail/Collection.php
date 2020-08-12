<?php
namespace Toppik\Report\Model\ResourceModel\Report\Subscription\Future\Detail;

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
            'Toppik\Report\Model\Report\Subscription\Future',
            'Toppik\Report\Model\ResourceModel\Report\Subscription\Future'
        );
        
        parent::__construct($entityFactory, $logger, $fetchStrategy, $eventManager, $connection, $resource);
        
        $this->storeManager = $storeManager;
    }
    
    protected function _initSelect() {
        parent::_initSelect();
        
        $this->getSelect()
            ->joinLeft(
                ['subscription' => 'subscriptions_profiles'],
                "(main_table.subscription_id = subscription.profile_id)",
                [
                    'subscription.customer_id AS subscription_customer_id',
                    'subscription.last_order_id',
                    'subscription.start_date AS subscription_start_date',
                    'subscription.sku AS subscription_sku'
                ]
            )
            ->joinLeft(
                ['customer' => 'customer_entity'],
                "(subscription.customer_id = customer.entity_id)",
                [
                    'customer.email AS customer_email',
                ]
            )
            ->joinLeft(
                ['order' => 'sales_order'],
                "(subscription.last_order_id = order.entity_id)",
                [
                    'order.increment_id AS subscription_last_order'
                ]
            );
        
        return $this;
    }
    
}
