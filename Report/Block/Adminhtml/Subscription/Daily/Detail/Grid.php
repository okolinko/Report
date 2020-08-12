<?php
namespace Toppik\Report\Block\Adminhtml\Subscription\Daily\Detail;

class Grid extends \Magento\Backend\Block\Widget\Grid\Container {
    
    protected function _construct() {
        $this->_blockGroup = 'Toppik_Report';
        $this->_controller = 'adminhtml_subscription_daily_detail';
        $this->_headerText = __('Subscription Daily Report Details');
        parent::_construct();
    }
	
}
