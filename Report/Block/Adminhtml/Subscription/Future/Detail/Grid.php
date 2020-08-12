<?php
namespace Toppik\Report\Block\Adminhtml\Subscription\Future\Detail;

class Grid extends \Magento\Backend\Block\Widget\Grid\Container {
    
    protected function _construct() {
        $this->_blockGroup = 'Toppik_Report';
        $this->_controller = 'adminhtml_subscription_future_detail';
        $this->_headerText = __('Subscription Future Report Details');
        parent::_construct();
    }
	
}
