<?php
namespace Toppik\Report\Controller\Debug;

use Magento\Framework\App\Action;
use Magento\Framework\App\Action\Context;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Controller\ResultInterface;

class OrderKit extends Action\Action {
    
    /**
     * @var \Toppik\Subscriptions\Helper\Data
     */
    protected $subscriptionHelper;
    
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
     * @var \Magento\Catalog\Model\ProductRepository
     */
    private $productRepository;
    
    protected $_storeManager;
    
    protected $_orderRepository;
    
    /**
     * @param Action\Context $context
     * @param PageFactory $resultPageFactory
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
		\Toppik\Subscriptions\Helper\Data $subscriptionHelper,
        \Magento\Framework\Event\ManagerInterface $eventManager,
        \Magento\Framework\App\ResourceConnection $resource,
        \Magento\Sales\Model\OrderFactory $orderFactory,
        \Magento\Catalog\Model\ProductRepository $productRepository,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Sales\Api\OrderRepositoryInterface $orderRepository
    ) {
		$this->subscriptionHelper = $subscriptionHelper;
        $this->eventManager = $eventManager;
        $this->resource = $resource;
        $this->orderFactory = $orderFactory;
        $this->productRepository = $productRepository;
        $this->_storeManager = $storeManager;
        $this->_orderRepository = $orderRepository;
        parent::__construct($context);
    }
	
    public function execute() {
        $messages = array();
        $items = array();
        $output = '';
        $order_id = (int) $this->getRequest()->getParam('id');
        $helper = $this->_objectManager->create('Toppik\Sap\Helper\ExportOrder');
        
        try {
            $list = $this->_orderRepository->getList($this->createOrderSearchCriteria($order_id));
            
            foreach($list->getItems() as $_item) {
                $messages[] = sprintf('Start order ID %s', $_item->getId());
                
                $output = shell_exec(sprintf('grep -R "After shipment save -> process order ID %s " %s', $_item->getId(), BP . '/var/log/*.log'));
                $output = str_replace(BP . '/var/log/', '', $output);
                
                foreach($_item->getAllVisibleItems() as $_order_item) {
                    $kits = $helper->getItemsBySku($_order_item->getSku(), $_order_item->getQtyOrdered());
                    
                    if(count($kits) > 0) {
                        $messages[] = sprintf('%s <strong>Order item ID "%s" with sku "%s" and qty "%s" has "%s" kit items</strong>', str_repeat('-', 5), $_order_item->getId(), $_order_item->getSku(), (int) $_order_item->getQtyOrdered(), count($kits));
                        
                        foreach($kits as $_sku => $_data) {
                            if(!isset($items[$_sku])) {
                                $items[$_sku] = 0;
                            }
                            
                            $items[$_sku] = $items[$_sku] + ((int) $_order_item->getQtyOrdered() * (int) $_data['qty']);
                            
                            $messages[] = sprintf('%s Item # "%s", price "%s", qty "%s" %s>> <strong>"%s" = "%s"</strong>', str_repeat('-', 10), $_sku, $_data['price'], $_data['qty'], str_repeat('=', 10), $_sku, ((int) $_order_item->getQtyOrdered() * (int) $_data['qty']));
                        }
                    } else {
                        if(!isset($items[$_order_item->getSku()])) {
                            $items[$_order_item->getSku()] = 0;
                        }
                        
                        $items[$_order_item->getSku()] = $items[$_order_item->getSku()] + (int) $_order_item->getQtyOrdered();
                        
                        $messages[] = sprintf('----- Order item ID "%s" with sku "%s" and qty "%s" does not have kit items', $_order_item->getId(), $_order_item->getSku(), (int) $_order_item->getQtyOrdered());
                    }
                }
            }
        } catch(\Exception $e) {
            $messages[] = sprintf('Error: %s', $e->getMessage());
        }
        
        $messages[] = sprintf('End for order ID %s', $order_id);
        
        echo sprintf('<div style="float: left; width: 69%%; border-right: 1px solid #ccc; margin-right: 1%%; padding-right: 20px; box-sizing: border-box;"><h3 style="border-bottom: 1px solid #ccc; padding-bottom: 10px; margin-bottom: 30px;">Order Items Breakdown</h3><pre>%s</pre></div>', print_r($messages, true));
        echo sprintf('<div style="float: right; width: 30%%; box-sizing: border-box;"><h3 style="border-bottom: 1px solid #ccc; padding-bottom: 10px; margin-bottom: 30px;">Final result for SAP queue:</h3><pre>%s</pre></div>', print_r($items, true));
        echo sprintf('<div style="clear: both; width: 100%%; border-right: 1px solid #ccc; margin-right: 1%%; padding-right: 20px; box-sizing: border-box;"><h3 style="border-bottom: 1px solid #ccc; padding-bottom: 10px; margin-bottom: 30px;">CartRover | WMS Check</h3><pre>%s</pre></div>', print_r($output, true));
        exit;
    }
    
    private function createOrderSearchCriteria($order_id) {
        $searchCriteria = $this->_objectManager->create('Magento\Framework\Api\SearchCriteria');
        
        $filter = $this->createFilter('entity_id', 'eq', $order_id);
        
        $searchCriteria->setFilterGroups(array($this->createFilterGroup(array($filter))));
        
        return $searchCriteria;
    }
    
    private function createFilter($field, $operator, $value) {
        $filter = $this->_objectManager->create('Magento\Framework\Api\Filter');
        
        $filter->setField($field)
                ->setConditionType($operator)
                ->setValue($value);
        
        return $filter;
    }
    
    private function createFilterGroup($filters) {
        $filterGroup = $this->_objectManager->create('Magento\Framework\Api\Search\FilterGroup');
        
        $filterGroup->setFilters($filters);
        
        return $filterGroup;
    }
    
}
