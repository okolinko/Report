<?php
namespace Toppik\Report\Model\Report\Subscription;

class Monthly extends \Magento\Framework\Model\AbstractModel {
	
    /**
     * Initialize resource model
     * @return void
     */
    protected function _construct() {
        $this->_init('Toppik\Report\Model\ResourceModel\Report\Subscription\Monthly');
    }
	
}
