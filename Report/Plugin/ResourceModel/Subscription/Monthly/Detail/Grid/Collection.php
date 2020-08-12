<?php
namespace Toppik\Report\Plugin\ResourceModel\Subscription\Monthly\Detail\Grid;

class Collection {
	
    public function afterSetMainTable(\Toppik\Report\Model\ResourceModel\Report\Subscription\Monthly\Detail\Collection $collection) {
        $collection->addFilterToMap('created_at', 'main_table.created_at');
        $collection->addFilterToMap('source', 'orders.source');
        $collection->addFilterToMap('merchant_source', 'orders.merchant_source');
        $collection->addFilterToMap('order_real_id', 'orders.entity_id');
        $collection->addFilterToMap('increment_id', 'main_table.increment_id');
        $collection->addFilterToMap('customer_id', 'main_table.customer_id');
        $collection->addFilterToMap('customer_email', 'customer.email');
    }
	
}
