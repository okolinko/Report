<?php
namespace Toppik\Report\Controller\Debug;

use Magento\Framework\App\Action;
use Magento\Framework\App\Action\Context;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Controller\ResultInterface;

class Braintree extends Action\Action {
	
    /**
     * @var ManagerInterface
     */
    private $eventManager;
	
    /**
     * @var ObjectManagerInterface
     */
    protected $_objectManager;
	
    /**
     * @var \Magento\Braintree\Model\Adapter\BraintreeAdapterFactory
     */
    private $braintreeAdapter;
    
    /**
     * @param Action\Context $context
     * @param PageFactory $resultPageFactory
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Framework\Event\ManagerInterface $eventManager,
        \Magento\Framework\ObjectManagerInterface $objectManager,
        \Magento\Braintree\Model\Adapter\BraintreeAdapterFactory $braintreeAdapter
    ) {
        $this->eventManager = $eventManager;
        $this->_objectManager = $objectManager;
        $this->braintreeAdapter = $braintreeAdapter->create();
        parent::__construct($context);
    }
	
    public function execute() {
        $value = null;
        $start = microtime(true); 
        
        try {
            $id = isset($_GET['_txn_id_']) && strlen($_GET['_txn_id_']) < 10 ? $_GET['_txn_id_'] : null;
            
            if($id) {
                $value = \Braintree\Transaction::find($id);
            }
        } catch(\Exception $e) {
            $value = $e->getMessage();
        }
        
        $end = microtime(true);
        
        if($value) {
            echo sprintf('Execution time %s<br />', ($end - $start));
            echo '<pre>';print_r($value);exit;
        }
    }
	
}
