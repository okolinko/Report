<?php
namespace Toppik\Report\Model\ResourceModel\Report\Subscription;

class Index extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb {
	
	const MAIN_TABLE = 'subscriptions_profiles';
	
    /**
     * Resource initialization
     *
     * @return void
     */
    protected function _construct() {
        $this->_init(self::MAIN_TABLE, 'entity_id');
    }
	
}
