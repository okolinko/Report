<?php
namespace Toppik\Report\Controller\Adminhtml\Subscription;

class Lifetime extends \Toppik\Report\Controller\Adminhtml\Index {
	
    /**
     * Sales report action
     *
     * @return void
     */
    public function execute() {
        $this->_initAction()->_setActiveMenu(
            'Toppik_Report::toppik_report'
        )->_addBreadcrumb(
            __('Toppik Report'),
            __('Toppik Report')
        );
		
        $this->_view->getPage()->getConfig()->getTitle()->prepend(__('Subscription Lifetime Report'));
        
        $gridBlock = $this->_view->getLayout()->getBlock('adminhtml_subscription_lifetime.grid');
        $filterFormBlock = $this->_view->getLayout()->getBlock('grid.filter.form');
        
        $this->_initReportAction([$gridBlock, $filterFormBlock]);
        
        $this->_view->renderLayout();
    }
    
}
