<?php
namespace Toppik\Report\Model\ResourceModel\Report\Subscription;

class Pivot extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb {
	
	const MAIN_TABLE = 'subscriptions_profiles';
	
    /**
     * Resource initialization
     *
     * @return void
     */
    protected function _construct() {
        $this->_init(self::MAIN_TABLE, 'profile_id');
    }
	
}
