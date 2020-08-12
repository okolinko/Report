<?php
namespace Toppik\Report\Plugin\ResourceModel\Subscription\Future\Detail\Grid;

class Collection {
	
    public function afterSetMainTable(\Toppik\Report\Model\ResourceModel\Report\Subscription\Future\Detail\Collection $collection) {
        $collection->addFilterToMap('date', 'main_table.date');
        $collection->addFilterToMap('subscription_id', 'main_table.subscription_id');
        $collection->addFilterToMap('subscription_period', 'main_table.subscription_period');
        $collection->addFilterToMap('subscription_total', 'main_table.subscription_total');
        $collection->addFilterToMap('subscription_merchant_source', 'main_table.subscription_merchant_source');
        $collection->addFilterToMap('subscription_customer_id', 'subscription.customer_id');
        $collection->addFilterToMap('last_order_id', 'subscription.last_order_id');
        $collection->addFilterToMap('subscription_start_date', 'subscription.start_date');
        $collection->addFilterToMap('subscription_sku', 'subscription.sku');
        $collection->addFilterToMap('subscription_last_order', 'order.increment_id');
        $collection->addFilterToMap('customer_email', 'customer.email');
    }
	
}
