<?php
namespace Toppik\Report\Controller\Adminhtml\Subscription;

class Average extends \Toppik\Report\Controller\Adminhtml\Index {
	
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
		
        $this->_view->getPage()->getConfig()->getTitle()->prepend(__('Subscription Average Report'));
        $this->_initReportAction($this->_view->getLayout()->getBlock('adminhtml_subscription_average.grid'));
        $this->_view->renderLayout();
    }
	
    /**
     * Report action init operations
     *
     * @param array|\Magento\Framework\DataObject $blocks
     * @return $this
     */
    public function _initReportAction($blocks) {
        if(!is_array($blocks)) {
            $blocks = [$blocks];
        }
		
        foreach($blocks as $block) {
            if($block) {
                $block->setFilterData(new \Magento\Framework\DataObject());
            }
        }
		
        return $this;
    }
	
}
