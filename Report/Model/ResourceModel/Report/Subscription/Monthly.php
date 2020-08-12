<?php
namespace Toppik\Report\Model\ResourceModel\Report\Subscription;

class Monthly extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb {
	
	const MAIN_TABLE = 'sales_order_grid';
	
    /**
     * Resource initialization
     *
     * @return void
     */
    protected function _construct() {
        $this->_init(self::MAIN_TABLE, 'entity_id');
    }
	
}
