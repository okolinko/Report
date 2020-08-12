<?php
namespace Toppik\Report\Block\Adminhtml\Subscription\Average\Detail;

class Grid extends \Magento\Backend\Block\Widget\Grid\Container {
    
    protected function _construct() {
        $this->_blockGroup = 'Toppik_Report';
        $this->_controller = 'adminhtml_subscription_average_detail';
        $this->_headerText = __('Subscription Average Report Details');
        parent::_construct();
    }
	
}
