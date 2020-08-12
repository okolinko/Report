<?php
namespace Toppik\Report\Controller\Debug;

use Magento\Framework\App\Action;
use Magento\Framework\App\Action\Context;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Controller\ResultInterface;

class InvoiceFix extends Action\Action {
	
    /**
     * @var ManagerInterface
     */
    private $eventManager;
	
    /**
     * @var ObjectManagerInterface
     */
    protected $_objectManager;
    
    /**
     * @param Action\Context $context
     * @param PageFactory $resultPageFactory
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Framework\Event\ManagerInterface $eventManager,
        \Magento\Framework\ObjectManagerInterface $objectManager
    ) {
        $this->eventManager = $eventManager;
        $this->_objectManager = $objectManager;
        parent::__construct($context);
    }
	
    public function execute() {
        try {
            $collection = $this->_getPendingQueue();
            
            foreach($collection as $_order_id) {
                try {
                    echo sprintf('Start processing order ID %s<br />', $_order_id);
                    
                    $order = $this->_objectManager->create('Magento\Sales\Model\Order')->load($_order_id);
                    
                    if(!$order->getId()) {
                        throw new \Exception(sprintf('Unknown order ID %s', $_order_id));
                    }
                    
                    if($order->getTotalPaid() > 0.001) {
                        throw new \Exception(sprintf('Order ID %s already been paid with amount %s', $_order_id, $order->getTotalPaid()));
                    }
                    
                    $order->setTotalPaid($order->getGrandTotal());
                    $order->setBaseTotalPaid($order->getBaseGrandTotal());
                    
                    $order->save();
                    
                    $message = sprintf(
                        'Updated order ID %s and increment ID %s with our automation tool',
                        $order->getId(),
                        $order->getIncrementId()
                    );
                    
                    echo sprintf('%s %s<br />', str_repeat('-', 10), $message);
                } catch(\Exception $e) {
                    echo sprintf('----- Loop: %s', $e->getMessage());
                }
            }
        } catch(\Exception $e) {
            echo sprintf('Global: %s', $e->getMessage());
        }
    }
	
	protected function _getPendingQueue() {
        return array();
	}
    
}
