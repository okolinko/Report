<?php
namespace Toppik\Report\Controller\Adminhtml\Subscription\Monthly;

class Detail extends \Toppik\Report\Controller\Adminhtml\Subscription\AbstractDetail {
	
    protected $_allowedParams = array('created_at_from', 'created_at_to', 'source');
    
    public function execute() {
        $data       = $this->_initParams();
        $resultPage = $this->_initAction();
        $resultPage->getConfig()->getTitle()->prepend(__('Subscription Monthly Report Details%1', (count($data) ? sprintf(': %s', implode(', ', $data)) : '')));
        return $resultPage;
    }
	
}
