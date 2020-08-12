<?php
namespace Toppik\Report\Controller\Debug;

use Magento\Framework\App\Action;
use Magento\Framework\App\Action\Context;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Controller\ResultInterface;

class Invoice extends Action\Action {
	
    /**
     * @var ManagerInterface
     */
    private $eventManager;
	
    /**
     * @var ObjectManagerInterface
     */
    protected $_objectManager;
	
    /**
     * @var \Magento\Sales\Model\Service\InvoiceService
     */
    private $invoiceService;
    
    /**
     * @var \Magento\Framework\DB\TransactionFactory
     */
    private $transactionFactory;
    
    /**
     * @param Action\Context $context
     * @param PageFactory $resultPageFactory
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Framework\Event\ManagerInterface $eventManager,
        \Magento\Framework\ObjectManagerInterface $objectManager,
        \Magento\Sales\Model\Service\InvoiceService $invoiceService,
        \Magento\Framework\DB\TransactionFactory $transactionFactory
    ) {
        $this->eventManager = $eventManager;
        $this->_objectManager = $objectManager;
        $this->invoiceService = $invoiceService;
        $this->transactionFactory = $transactionFactory;
        parent::__construct($context);
    }
	
    public function execute() {
        try {
            $should_process = (int) $this->getRequest()->getParam('p') === 1;
            
            $collection = $this->_getPendingQueue();
            
            foreach($collection as $_order_id) {
                try {
                    echo sprintf('Start processing order ID %s', $_order_id);
                    
                    $order = $this->_objectManager->create('Magento\Sales\Model\Order')->load($_order_id);
                    
                    if(!$order->getId()) {
                        throw new \Exception(sprintf('Unknown order ID %s', $_order_id));
                    }
                    
                    if($should_process === true) {
                        $state = $order->getState();
                        
                        $order->setState(\Magento\Sales\Model\Order::STATE_PROCESSING);
                        
                        foreach($order->getAllVisibleItems() as $_orderItem) {
                            $_orderItem->setQtyInvoiced(0);
                        }
                        
                        $invoice = $this->_invoiceOrder($order);
                        
                        $order->setState($state);
                        
                        $message = sprintf(
                            'Invoiced %s order ID %s and increment ID %s with our automation tool: invoice grand total %s',
                            ($invoice->getRequestedCaptureCase() !== \Magento\Sales\Model\Order\Invoice::CAPTURE_OFFLINE ? 'online' : 'offline'),
                            $order->getId(),
                            $order->getIncrementId(),
                            ($invoice ? $invoice->getBaseGrandTotal() : 0)
                        );
                        
                        $order->addStatusHistoryComment($message);
                        $order->save();
                        
                        echo sprintf('%s %s<br />', str_repeat('-', 10), $message);
                    }
                } catch(\Exception $e) {
                    echo sprintf('----- Loop: %s', $e->getMessage());
                }
            }
        } catch(\Exception $e) {
            echo sprintf('Global: %s', $e->getMessage());
        }
    }
	
	protected function _getPendingQueue() {
		$collection = array();
        
        $resource   = $this->_objectManager->get('Magento\Framework\App\ResourceConnection');
        
		$data       = $resource->getConnection(\Magento\Framework\App\ResourceConnection::DEFAULT_CONNECTION)->fetchAll(
			sprintf(
				'SELECT o.entity_id
                FROM %s AS o
                LEFT JOIN %s AS i ON i.order_id = o.entity_id
                WHERE o.`created_at` >= "2018-03-01 00:00:00" AND o.`merchant_source` = "triton" AND o.`status` = "complete" AND i.entity_id IS NULL',
				$resource->getTableName('sales_order'),
				$resource->getTableName('sales_invoice')
			)
		);
		
		if(count($data)) {
			foreach($data as $_item) {
                if(isset($_item['entity_id'])) {
                    $collection[] = $_item['entity_id'];
                }
			}
		}
		
		return $collection;
	}
	
    protected function _invoiceOrder($order) {
        if(!$order->canInvoice()) {
            throw new \Exception(sprintf('invoicing order -> canInvoice returned false for order %s', $order->getIncrementId()));
        }
        
        $invoice = $this->invoiceService->prepareInvoice($order);
        
        $invoice->setRequestedCaptureCase(\Magento\Sales\Model\Order\Invoice::CAPTURE_OFFLINE);
        
        $invoice->register();
        
        /*
         * Do not capture offline invoices
         */
        if($invoice->getRequestedCaptureCase() !== \Magento\Sales\Model\Order\Invoice::CAPTURE_OFFLINE) {
            $invoice->capture();
        }
        
        /* @var \Magento\Framework\DB\Transaction $transaction */
        $transaction = $this->transactionFactory->create();
        
        $transaction
            ->addObject($invoice)
            ->addObject($invoice->getOrder());
        
        $transaction->save();
        
        return $invoice;
    }
	
}
