<?php
namespace Toppik\Report\Block\Adminhtml\Subscription\Index\Detail;

class Grid extends \Magento\Backend\Block\Widget\Grid\Container {
    
    protected function _construct() {
        $this->_blockGroup = 'Toppik_Report';
        $this->_controller = 'adminhtml_subscription_index_detail';
        $this->_headerText = __('Subscription Report Details');
        parent::_construct();
    }
	
}
