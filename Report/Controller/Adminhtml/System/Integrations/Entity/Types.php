<?php
namespace Toppik\Report\Controller\Adminhtml\System\Integrations\Entity;

class Types extends \Magento\Backend\App\Action {
	
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
        return $this->_authorization->isAllowed('Toppik_Report::system_integrations_entity_types');
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
        $resultPage->getConfig()->getTitle()->prepend(__('System Integrations Entity Types'));
        return $resultPage;
    }
	
}
