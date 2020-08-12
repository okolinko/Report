<?php
namespace Toppik\Report\Controller\Adminhtml\System\Integrations\Mark;

class Fixed extends \Magento\Backend\App\Action {
    
    /**
     * @var Filter
     */
    protected $filter;
    
    /**
     * @var CollectionFactory
     */
    protected $collectionFactory;
    
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Ui\Component\MassAction\Filter $filter,
        \Toppik\Report\Model\ResourceModel\Report\System\Integrations\CollectionFactory $collectionFactory
    ) {
		parent::__construct($context);
        $this->filter = $filter;
        $this->collectionFactory = $collectionFactory;
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
		try {
            $collection = $this->filter->getCollection($this->collectionFactory->create());
            
            foreach($collection as $_item) {
                $_item->setData('fixed', 1)->save();
            }
            
            $this->messageManager->addSuccess(__('%1 item(s) have been marked as fixed', count($collection)));
		} catch(\Exception $e) {
			$this->messageManager->addError(__('An error has occurred: %1', $e->getMessage()));
		}
        
        $resultRedirect = $this->resultRedirectFactory->create();
        return $resultRedirect->setPath('*/*/integrations');
    }
    
}
