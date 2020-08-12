<?php
namespace Toppik\Report\Controller\Adminhtml\System\Integrations\Entity;

class Save extends \Magento\Backend\App\Action {
    
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Framework\Translate\InlineInterface $translateInline,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory,
        \Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory,
        \Magento\Framework\View\Result\LayoutFactory $resultLayoutFactory,
        \Magento\Framework\Controller\Result\RawFactory $resultRawFactory
    ) {
		parent::__construct($context);
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
		try {
            $model                      = $this->_objectManager->create('\Toppik\Report\Model\System\Integrations\Entity\Types');
            
            $data                       = $this->getRequest()->getPost();
            
            $id                         = isset($data['id']) ? (int) $data['id'] : null;
            $entity_type_code           = isset($data['entity_type_code']) ? $data['entity_type_code'] : null;
            $entity_type_label          = isset($data['entity_type_label']) ? $data['entity_type_label'] : null;
            $entity_type_description    = isset($data['entity_type_description']) ? $data['entity_type_description'] : null;
            $admin_ids                  = isset($data['admin_ids']) ? $data['admin_ids'] : array();
            
            if($id) {
                $model->load($id);
                
                if(!$model->getId()) {
                    $this->messageManager->addError(__('Item ID %1 does not exist', $id));
                    $resultRedirect = $this->resultRedirectFactory->create();
                    return $resultRedirect->setPath('*/*/integrations_entity_types');
                }
            }
            
            $model
                ->setData('entity_type_code', $entity_type_code)
                ->setData('entity_type_label', $entity_type_label)
                ->setData('entity_type_description', $entity_type_description)
                ->setData('admin_ids', implode(',', $admin_ids))
                ->save();
            
            $this->messageManager->addSuccess(__('The item has been successfully saved'));
		} catch(\Exception $e) {
			$this->messageManager->addError(__('An error has occurred: %1', $e->getMessage()));
		}
        
        $resultRedirect = $this->resultRedirectFactory->create();
        return $resultRedirect->setPath('*/*/integrations_entity_types');
    }
    
}
