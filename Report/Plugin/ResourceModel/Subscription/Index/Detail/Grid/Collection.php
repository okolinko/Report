<?php
namespace Toppik\Report\Plugin\ResourceModel\Subscription\Index\Detail\Grid;

class Collection {
	
    public function afterSetMainTable(\Toppik\Report\Model\ResourceModel\Report\Subscription\Index\Detail\Collection $collection) {
        $collection->addFilterToMap('created_at', 'main_table.created_at');
        $collection->addFilterToMap('status', 'main_table.status');
        $collection->addFilterToMap('subscription_id', 'main_table.profile_id');
        $collection->addFilterToMap('subscription_period', 'main_table.frequency_length');
        $collection->addFilterToMap('subscription_total', 'main_table.grand_total');
        $collection->addFilterToMap('subscription_merchant_source', 'main_table.merchant_source');
        $collection->addFilterToMap('subscription_customer_id', 'main_table.customer_id');
        $collection->addFilterToMap('last_order_id', 'main_table.last_order_id');
        $collection->addFilterToMap('subscription_start_date', 'main_table.start_date');
        $collection->addFilterToMap('subscription_sku', 'main_table.sku');
        $collection->addFilterToMap('subscription_last_order', 'order.increment_id');
        $collection->addFilterToMap('customer_email', 'customer.email');
    }
	
}
