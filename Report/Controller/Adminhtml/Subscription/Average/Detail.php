<?php
namespace Toppik\Report\Controller\Adminhtml\Subscription\Average;

class Detail extends \Toppik\Report\Controller\Adminhtml\Subscription\AbstractDetail {
	
    protected $_allowedParams = array('subscription_status', 'subscription_merchant_source', 'subscription_period', 'sku');
    
    public function execute() {
        $data       = $this->_initParams();
        $resultPage = $this->_initAction();
        $resultPage->getConfig()->getTitle()->prepend(__('Subscription Average Report Details%1', (count($data) ? sprintf(': %s', implode(', ', $data)) : '')));
        return $resultPage;
    }
	
}
