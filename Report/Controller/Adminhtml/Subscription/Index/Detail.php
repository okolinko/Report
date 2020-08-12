<?php
namespace Toppik\Report\Controller\Adminhtml\Subscription\Index;

class Detail extends \Toppik\Report\Controller\Adminhtml\Subscription\AbstractDetail {
	
    protected $_allowedParams = array(
        'status',
        'subscription_merchant_source',
        'created_at_from',
        'created_at_to',
        'cancelled_at_from',
        'cancelled_at_to',
        'suspended_at_from',
        'suspended_at_to'
    );
    
    public function execute() {
        $data       = $this->_initParams();
        $resultPage = $this->_initAction();
        $resultPage->getConfig()->getTitle()->prepend(__('Subscription Report Details%1', (count($data) ? sprintf(': %s', implode(', ', $data)) : '')));
        return $resultPage;
    }
	
}
