<?php
namespace Toppik\Report\Controller\Debug;

use Magento\Framework\App\Action;
use Magento\Framework\App\Action\Context;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Controller\ResultInterface;

class SapTest extends Action\Action {
	
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
    
    /**
     * @param Action\Context $context
     * @param PageFactory $resultPageFactory
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Framework\Event\ManagerInterface $eventManager,
        \Magento\Framework\App\ResourceConnection $resource,
        \Magento\Sales\Model\OrderFactory $orderFactory,
        \Magento\Catalog\Model\ProductRepository $productRepository
    ) {
        $this->eventManager = $eventManager;
        $this->resource = $resource;
        $this->orderFactory = $orderFactory;
        $this->productRepository = $productRepository;
        parent::__construct($context);
    }
	
    public function execute() {
        $order_id = $this->getRequest()->getParam('order_id');
        
        if($order_id) {
            $order = $this->orderFactory->create()->load($order_id);
            
            if($order->getId()) {
                $order_items = $order->getAllVisibleItems();
                $items = array();
                
                $this->eventManager->dispatch(
                    'order_items_process_kit',
                    ['order' => $order]
                );
                
                if(is_array($order->getProcessedOrderItems()) && count($order->getProcessedOrderItems()) > 0) {
                    $order_items = $order->getProcessedOrderItems();
                }
                
                foreach($order_items as $_item) {
                    $product = $this->getProductBySku($_item->getSku());
                    
                    if($product) {
                        $items[] = array(
                            'orderItemId' => $_item->getId(),
                            'qty' => (int) $_item->getQtyOrdered(),
                            'extension_attributes' => array(
                                'sap_material_number' => $product->getSapMaterialNumber(),
                                'lot_code' => sprintf('%s-%s', $_item->getId(), $product->getSapMaterialNumber()),
                                'quantity' => (int) $_item->getQtyOrdered(),
                                'warehouse' => 'LIFG'
                            )
                        );
                    } else {
                        echo 'Unknown product ' . $_item->getSku();
                        exit;
                    }
                }
                
                $data = array(
                    'orderId' => $order->getId(),
                    'emailSent' => 0,
                    'items' => $items
                );
                
                echo '<pre>';
                var_export($data);
            }
        }
        
        exit;
    }
    
    public function getProductBySku($sku) {
        $product = null;
        
        try {
            $product = $this->productRepository->get($sku);
        } catch(\Exception $e) {
            $product = $this->_objectManager->create('\Magento\Catalog\Model\ResourceModel\Product\CollectionFactory')->create()
                            ->addAttributeToFilter('sap_material_number', $sku)
                            ->getFirstItem();
        }
        
        return $product;
    }
    
}
