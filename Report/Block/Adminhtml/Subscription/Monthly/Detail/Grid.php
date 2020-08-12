<?php
namespace Toppik\Report\Block\Adminhtml\Subscription\Monthly\Detail;

class Grid extends \Magento\Backend\Block\Widget\Grid\Container {
    
    protected function _construct() {
        $this->_blockGroup = 'Toppik_Report';
        $this->_controller = 'adminhtml_subscription_monthly_detail';
        $this->_headerText = __('Subscription Monthly Report Details');
        parent::_construct();
    }
	
}
