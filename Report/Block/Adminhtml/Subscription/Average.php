<?php
namespace Toppik\Report\Block\Adminhtml\Subscription;

class Average extends \Magento\Backend\Block\Widget\Grid\Container {
	
    /**
     * Template file
     *
     * @var string
     */
    protected $_template = 'Magento_Reports::report/grid/container.phtml';
	
    /**
     * {@inheritdoc}
     */
    protected function _construct() {
        $this->_blockGroup = 'Toppik_Report';
        $this->_controller = 'adminhtml_subscription_average';
        $this->_headerText = __('Total Report');
        parent::_construct();
		
        $this->buttonList->remove('add');
    }
	
}
