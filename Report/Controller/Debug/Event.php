<?php
namespace Toppik\Report\Controller\Debug;

class Event extends \Magento\Framework\App\Action\Action {
	
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
        $result = new \Magento\Framework\DataObject;
        $this->eventManager->dispatch('sales_create_order_from_quote', ['result' => $result]);
    }
	
}
