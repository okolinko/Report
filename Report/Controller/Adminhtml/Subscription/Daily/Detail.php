<?php
namespace Toppik\Report\Controller\Adminhtml\Subscription\Daily;

class Detail extends \Toppik\Report\Controller\Adminhtml\Subscription\AbstractDetail {
	
    protected $_allowedParams = array('date', 'subscription_merchant_source');
    
    public function execute() {
        $data       = $this->_initParams();
        $resultPage = $this->_initAction();
        $resultPage->getConfig()->getTitle()->prepend(__('Subscription Daily Report Details%1', (count($data) ? sprintf(': %s', implode(', ', $data)) : '')));
        return $resultPage;
    }
	
}
