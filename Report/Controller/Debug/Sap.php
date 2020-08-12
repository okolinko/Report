<?php
namespace Toppik\Report\Controller\Debug;

use Magento\Framework\App\Action;
use Magento\Framework\App\Action\Context;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Controller\ResultInterface;

class Sap extends Action\Action {
	
    /**
     * @var ManagerInterface
     */
    private $eventManager;
	
    /**
     * @var ResourceConnection
     */
    private $resource;
	
    /**
     * @var OrderFactory
     */
    private $orderFactory;
    
    /**
     * @param Action\Context $context
     * @param PageFactory $resultPageFactory
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Framework\Event\ManagerInterface $eventManager,
        \Magento\Framework\App\ResourceConnection $resource,
        \Magento\Sales\Model\OrderFactory $orderFactory
    ) {
        $this->eventManager = $eventManager;
        $this->resource = $resource;
        $this->orderFactory = $orderFactory;
        parent::__construct($context);
    }
	
    public function execute() {
		$cron = $this->_objectManager->get('Toppik\Sap\Cron\ExportOrderToSftp');
		$cron->execute();
        
		$cron = $this->_objectManager->get('Toppik\Sap\Cron\ExportPaymentToSftp');
		$cron->execute();
    }
    
}
