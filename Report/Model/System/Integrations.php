<?php
namespace Toppik\Report\Model\System;

class Integrations extends \Magento\Framework\Model\AbstractModel {
    
    /**
     * Initialize resource model
     * @return void
     */
    protected function _construct() {
        $this->_init('\Toppik\Report\Model\ResourceModel\Report\System\Integrations');
    }
    
}
