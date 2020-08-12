<?php
namespace Toppik\Report\Controller\Adminhtml\System;

class Integrations extends \Magento\Backend\App\Action {
	
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory
    ) {
		parent::__construct($context);
        $this->resultPageFactory = $resultPageFactory;
    }
    
    /**
     * @return bool
     */
    protected function _isAllowed() {
        return $this->_authorization->isAllowed('Toppik_Report::system_integrations');
    }
	
    /**
     * @return $this
     */
    protected function _initAction() {
        $resultPage = $this->resultPageFactory->create();
        return $resultPage;
    }
	
    public function execute() {
        $resultPage = $this->_initAction();
        $resultPage->getConfig()->getTitle()->prepend(__('System Integrations'));
        return $resultPage;
    }
	
}
