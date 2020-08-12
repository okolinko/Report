<?php
namespace Toppik\Report\Controller\Adminhtml\Subscription;

class Refresh extends \Magento\Backend\App\Action {
	
    /**
     * Sales report action
     *
     * @return void
     */
    public function execute() {
		$this->_objectManager->get('Toppik\Report\Cron\Refresh')->execute();
        
        $resultRedirect = $this->resultRedirectFactory->create();
        $resultRedirect->setRefererUrl();
        return $resultRedirect;
    }
	
}
