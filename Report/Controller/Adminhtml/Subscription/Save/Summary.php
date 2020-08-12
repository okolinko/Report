<?php
namespace Toppik\Report\Controller\Adminhtml\Subscription\Save;

class Summary extends \Toppik\Report\Controller\Adminhtml\Subscription\AbstractDetail {
    
    protected $_allowedParams = array();
    
    public function execute() {
        $data       = $this->_initParams();
        $resultPage = $this->_initAction();
        $resultPage->getConfig()->getTitle()->prepend(__('Subscription Save Summary Report%1', (count($data) ? sprintf(': %s', implode(', ', $data)) : '')));
        return $resultPage;
    }
	
}
