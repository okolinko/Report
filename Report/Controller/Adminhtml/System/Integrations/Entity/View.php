<?php
namespace Toppik\Report\Controller\Adminhtml\System\Integrations\Entity;

class View extends \Magento\Backend\App\Action {
    
    protected $_coreRegistry;
	
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Framework\Registry $coreRegistry,
        \Magento\Framework\App\Response\Http\FileFactory $fileFactory,
        \Magento\Framework\Translate\InlineInterface $translateInline,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory,
        \Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory,
        \Magento\Framework\View\Result\LayoutFactory $resultLayoutFactory,
        \Magento\Framework\Controller\Result\RawFactory $resultRawFactory
    ) {
		parent::__construct($context);
        $this->_coreRegistry = $coreRegistry;
        $this->_fileFactory = $fileFactory;
        $this->_translateInline = $translateInline;
        $this->resultPageFactory = $resultPageFactory;
        $this->resultJsonFactory = $resultJsonFactory;
        $this->resultLayoutFactory = $resultLayoutFactory;
        $this->resultRawFactory = $resultRawFactory;
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
        $id = (int) $this->getRequest()->getParam('id');
        
        $model = $this->_objectManager->create('\Toppik\Report\Model\System\Integrations\Entity\Types');
        
        if($id) {
            $model->load($id);
        }
        
        $this->_coreRegistry->register('entity_type_item', $model);
        
        $resultPage = $this->_initAction();
        
        $resultPage->addBreadcrumb(__('Edit Entity Type'), __('Edit Entity Type'));
        $resultPage->getConfig()->getTitle()->prepend(__('System Integrations Entity Types'));
        $resultPage->getConfig()->getTitle()->prepend(__('Edit Entity Type'));
        
        return $resultPage;
    }
    
}
