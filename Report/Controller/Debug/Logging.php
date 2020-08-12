<?php
namespace Toppik\Report\Controller\Debug;

use Magento\Framework\App\Action;
use Magento\Framework\App\Action\Context;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Controller\ResultInterface;

class Logging extends Action\Action {
	
    /**
     * @var ResourceConnection
     */
    protected $_resource;
	
    /**
     * @var \Magento\Framework\App\Response\Http\FileFactory
     */
    protected $_fileFactory;
	
    /**
     * @param Action\Context $context
     * @param PageFactory $resultPageFactory
     */
    public function __construct(
        Action\Context $context,
        \Magento\Framework\App\ResourceConnection $resource,
        \Magento\Framework\App\Response\Http\FileFactory $fileFactory
    ) {
        $this->_resource = $resource;
        $this->_fileFactory = $fileFactory;
        parent::__construct($context);
    }
	
    public function execute() {
		switch($this->getRequest()->getParam('_t_')) {
			case 'subscriptions':
				$data = file_exists(BP . '/var/log/subscriptions.log') ? file_get_contents(BP . '/var/log/subscriptions.log') : '';
				return $this->_fileFactory->create('subscriptions.log', $data, \Magento\Framework\App\Filesystem\DirectoryList::VAR_DIR);
			
			case 'edge_send':
				$data = file_exists(BP . '/var/log/edge_export_order.log') ? file_get_contents(BP . '/var/log/edge_export_order.log') : '';
				return $this->_fileFactory->create('edge_export_order.log', $data, \Magento\Framework\App\Filesystem\DirectoryList::VAR_DIR);
			
			case 'edge_receive':
				$data = file_exists(BP . '/var/log/edge_import_sn.log') ? file_get_contents(BP . '/var/log/edge_import_sn.log') : '';
				return $this->_fileFactory->create('edge_import_sn.log', $data, \Magento\Framework\App\Filesystem\DirectoryList::VAR_DIR);
			
			case 'ms':
				$data = file_exists(BP . '/var/log/microsite-import-order.log') ? file_get_contents(BP . '/var/log/microsite-import-order.log') : '';
				return $this->_fileFactory->create('microsite-import-order.log', $data, \Magento\Framework\App\Filesystem\DirectoryList::VAR_DIR);
            
			case 'sap':
				$data = file_exists(BP . '/var/log/sap.log') ? file_get_contents(BP . '/var/log/sap.log') : '';
				return $this->_fileFactory->create('sap.log', $data, \Magento\Framework\App\Filesystem\DirectoryList::VAR_DIR);
            
			case 'apilogger':
				$data = file_exists(BP . '/var/log/apilogger.log') ? file_get_contents(BP . '/var/log/apilogger.log') : '';
				return $this->_fileFactory->create('apilogger.log', $data, \Magento\Framework\App\Filesystem\DirectoryList::VAR_DIR);
		}
		
		$resultRedirect = $this->resultRedirectFactory->create();
		return $resultRedirect->setPath('/');
    }
	
}
