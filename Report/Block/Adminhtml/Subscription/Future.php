<?php
namespace Toppik\Report\Block\Adminhtml\Subscription;

class Future extends \Magento\Backend\Block\Widget\Grid\Container {
	
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
        $this->_controller = 'adminhtml_subscription_future';
        $this->_headerText = __('Total Report');
        parent::_construct();
        
        $this->buttonList->remove('add');
        
		// $refreshUrl = $this->getUrl('*/*/refresh', ['_current' => true]);
		
        /* $this->addButton(
            'refresh_button',
            ['label' => __('Refresh'), 'onclick' => "window.location.href = '{$refreshUrl}'; return false;", 'class' => 'primary']
        ); */
        
        $this->addButton(
            'filter_form_submit',
            ['label' => __('Show Report'), 'onclick' => 'filterFormSubmit()', 'class' => 'primary']
        );
    }
	
    /**
     * Get filter URL
     *
     * @return string
     */
    public function getFilterUrl() {
        $this->getRequest()->setParam('filter', null);
        return $this->getUrl('*/*/future', ['_current' => true]);
    }
    
}
