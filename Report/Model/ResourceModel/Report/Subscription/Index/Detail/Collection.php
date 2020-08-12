<?php
namespace Toppik\Report\Model\ResourceModel\Report\Subscription\Index\Detail;

class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection {
	
    /**
     * @var string
     */
    protected $_idFieldName = 'profile_id';
    
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
            'Toppik\Report\Model\Report\Subscription\Index',
            'Toppik\Report\Model\ResourceModel\Report\Subscription\Index'
        );
        
        parent::__construct($entityFactory, $logger, $fetchStrategy, $eventManager, $connection, $resource);
        
        $this->storeManager = $storeManager;
    }
    
    protected function _initSelect() {
        parent::_initSelect();
        
        $this->getSelect()
            ->columns(array(
                'subscription_period'           => new \Zend_Db_Expr('FLOOR(main_table.frequency_length / (60 * 60 * 24))'),
                'subscription_id'               => 'main_table.profile_id',
                'subscription_total'            => 'main_table.grand_total',
                'subscription_status'           => 'main_table.status',
                'subscription_merchant_source'  => 'main_table.merchant_source',
                'subscription_customer_id'      => 'main_table.customer_id',
                'last_order_id'                 => 'main_table.last_order_id',
                'last_order_at'                 => 'main_table.last_order_at',
                'subscription_start_date'       => 'main_table.start_date',
                'subscription_sku'              => 'main_table.sku',
                'subscription_cancelled_at'     => 'main_table.cancelled_at',
                'subscription_suspended_at'     => 'main_table.suspended_at',
                'last_suspend_error'            => 'main_table.last_suspend_error',
                'subscription_last_order'       => 'order.entity_id',
                'subscription_last_order_at'    => 'order.created_at',
                'customer_email'                => 'customer.email',
                'last_shipment_date'            => 'shipment.last_shipment_date',
                'count_orders'                  => 'spo.count_orders',
                'sum_orders'                    => new \Zend_Db_Expr('(spo.count_orders * main_table.grand_total)')
            ))
            
            ->joinLeft(
                ['customer' => 'customer_entity'],
                "(main_table.customer_id = customer.entity_id)",
                [
                    'customer.email AS customer_email',
                ]
            )
            
            ->joinLeft(
                [
                    'spo' => new \Zend_Db_Expr('
                        (
                            SELECT so.profile_id, COUNT(o.entity_id) AS count_orders, SUM(o.subtotal) AS sum_orders
                            FROM subscriptions_profiles_orders AS so
                            INNER JOIN sales_order AS o ON o.entity_id = so.order_id
                            GROUP BY so.profile_id
                        )
                    ')
                ],
                "(main_table.profile_id = spo.profile_id)",
                [
                    'spo.count_orders AS count_orders',
                    '(spo.count_orders * main_table.grand_total) AS sum_orders'
                ]
            )
            
            ->joinLeft(
                ['shipment' => new \Zend_Db_Expr('(SELECT order_id, MAX(created_at) AS last_shipment_date FROM sales_shipment GROUP BY order_id)')],
                "(main_table.last_order_id = shipment.order_id)",
                [
                    'shipment.last_shipment_date AS last_shipment_date',
                ]
            )
            
            ->joinLeft(
                ['order' => 'sales_order'],
                "(main_table.last_order_id = order.entity_id)",
                [
                    'order.increment_id AS subscription_last_order',
                    'order.created_at AS subscription_last_order_at'
                ]
            );
        
        return $this;
    }
    
}
