<?php
namespace Toppik\Report\Controller\Adminhtml\Subscription;

class RefreshFuture extends \Magento\Backend\App\Action {
    
    public function execute() {
		$this->_objectManager->get('\Toppik\Report\Model\Subscription\Future')->refresh();
        /** @var \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
        $resultRedirect = $this->resultFactory->create(\Magento\Framework\Controller\ResultFactory::TYPE_REDIRECT);
        return $resultRedirect->setPath('*/*/future');
    }
	
}
