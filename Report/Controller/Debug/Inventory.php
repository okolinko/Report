<?php
namespace Toppik\Report\Controller\Debug;

use Magento\Framework\App\Action;
use Magento\Framework\App\Action\Context;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Controller\ResultInterface;

class Inventory extends Action\Action {
	
    /**
     * @var ManagerInterface
     */
    private $eventManager;
	
    /**
     * @param Action\Context $context
     * @param PageFactory $resultPageFactory
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Framework\Event\ManagerInterface $eventManager
    ) {
        $this->eventManager = $eventManager;
        parent::__construct($context);
    }
	
    public function execute() {
        try {
            $this->_objectManager->get('Toppik\Inventory\Cron\Import')->execute();
        } catch(\Exception $e) {
            echo $e->getMessage();
        }
        
        exit;
    }
	
}
