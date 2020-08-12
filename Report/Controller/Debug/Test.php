<?php
namespace Toppik\Report\Controller\Debug;

use Magento\Framework\App\Action;
use Magento\Framework\App\Action\Context;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Controller\ResultInterface;

class Test extends Action\Action {
    
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
        $update = (int) $this->getRequest()->getParam('update');
        $order_id = $this->getRequest()->getParam('order_id');
        
        if($order_id) {
            $order = $this->orderFactory->create()->load($order_id);
            
            if($order->getId()) {
                
                foreach($order->getAllVisibleItems() as $_item) {
                    $item_data = $_item->getProductOptions();
                    
                    if(is_array($item_data) && isset($item_data['info_buyRequest']) && isset($item_data['info_buyRequest']['product'])) {
                        $product_id = (int) $item_data['info_buyRequest']['product'];
                        
                        if($product_id) {
                            if($product_id !== $_item->getProductId()) {
                                $product = $this->getProductById($product_id);

                                if(!$product) {
                                    echo sprintf('Can\'t process order because of unknown product ID %s (%s)', $_item->getProductId(), $_item->getSku());
                                    exit;
                                }

                                echo sprintf(
                                    'Found product to update:<br />item_id = "%s",<br />current product_id = "%s",<br />supposed product_id = "%s",<br />price = "%s"<br />row_total = "%s"<br />tax_amount = "%s"<br /><br />',
                                    $_item->getId(),
                                    $_item->getProductId(),
                                    $product_id,
                                    $product->getFinalPrice(),
                                    $product->getFinalPrice() * $_item->getQtyOrdered(),
                                    ''
                                );
                            }
                        }
                    }
                }
                
                if(count($order->getAllVisibleItems()) !== 1) {
                    echo sprintf('Can\'t process order because of items count (%s)', count($order->getAllVisibleItems()));
                    exit;
                }
                
                $sap_items = $this->getItemsFromQueue($order->getId());
                
                if(count($sap_items) < count($order->getAllVisibleItems())) {
                    echo sprintf('Can\'t process order because sap items do not exist (%s)', count($sap_items));
                    exit;
                }
                
                $sap_price = 0;
                $sap_tax = 0;
                
                foreach($sap_items as $_item) {
                    $sap_price = $sap_price + $_item->getItemPrice();
                    $sap_tax = $sap_tax + $_item->getItemTax();
                }
                
                foreach($order->getAllVisibleItems() as $_item) {
                    $item_data = $_item->getProductOptions();
                    
                    if(is_array($item_data) && isset($item_data['info_buyRequest']) && isset($item_data['info_buyRequest']['product'])) {
                        $product_id = (int) $item_data['info_buyRequest']['product'];
                        
                        if($product_id) {
                            if($product_id !== $_item->getProductId()) {
                                echo sprintf(
                                    'Found product to update:<br />item_id = "%s",<br />current product_id = "%s", supposed product_id = "%s"<br /><br />',
                                    $_item->getId(),
                                    $_item->getProductId(),
                                    $product_id
                                );
                                
                                $product = $this->getProductById($product_id);
                                
                                if(!$product) {
                                    echo sprintf('Can\'t process order because of unknown product ID %s (%s)', $_item->getProductId(), $_item->getSku());
                                    exit;
                                }
                            }
                        }
                    }
                    
                    echo sprintf(
                        'Current Item # "%s" (%s)<br />price = "%s",<br />base_price = "%s",<br />original_price = "%s",<br />base_original_price = "%s",<br />tax_amount = "%s",<br />base_tax_amount = "%s",<br />qty = "%s",<br />row_total = "%s",<br />base_row_total = "%s"<br /><br />',
                        $_item->getSku(),
                        $_item->getProductId(),
                        $_item->getPrice(),
                        $_item->getBasePrice(),
                        $_item->getOriginalPrice(),
                        $_item->getBaseOriginalPrice(),
                        $_item->getTaxAmount(),
                        $_item->getBaseTaxAmount(),
                        $_item->getQtyOrdered(),
                        $_item->getRowTotal(),
                        $_item->getBaseRowTotal()
                    );
                    
                    echo sprintf(
                        'ORIGINAL Item # "%s" (%s)<br />price = "%s",<br />base_price = "%s",<br />original_price = "%s",<br />base_original_price = "%s",<br />tax_amount = "%s",<br />base_tax_amount = "%s",<br />qty = "%s",<br />row_total = "%s",<br />base_row_total = "%s"<br /><br /><hr /><br />',
                        $_item->getSku(),
                        $_item->getProductId(),
                        $sap_price,
                        $sap_price,
                        $sap_price,
                        $sap_price,
                        $sap_tax,
                        $sap_tax,
                        $_item->getQtyOrdered(),
                        $sap_price * $_item->getQtyOrdered(),
                        $sap_price * $_item->getQtyOrdered()
                    );
                    
                    if($update === 1) {
                        $_item
                            ->setPrice($sap_price)
                            ->setBasePrice($sap_price)
                            ->setOriginalPrice($sap_price)
                            ->setBaseOriginalPrice($sap_price)
                            ->setTaxAmount($sap_tax)
                            ->setBaseTaxAmount($sap_tax)
                            ->setRowTotal($sap_price * $_item->getQtyOrdered())
                            ->setBaseRowTotal($sap_price * $_item->getQtyOrdered())
                            ->save();
                        
                        echo sprintf('<strong>Updated item ID %s</strong><br />', $_item->getId());
                        echo sprintf('<a href="%s">Back</a>', $this->_url->getUrl('toppikreport/debug/test'), $_item->getId());
                    } else {
                        echo sprintf('<a href="%s">Update Item ID %s</a>', $this->_url->getUrl('toppikreport/debug/test', array('order_id' => $order_id, 'update' => 1)), $_item->getId());
                    }
                    
                    break;
                }
            }
        } else {
            echo sprintf(
                '<input type="text" name="order_id" id="order_id" /><a href="" onclick = "if(document.getElementById(\'order_id\').value) {window.location.href = \'%sorder_id/\' + document.getElementById(\'order_id\').value; return false;} return false;">Check</a>',
                $this->_url->getUrl('toppikreport/debug/test/')
            );
        }
        
        exit;
    }
    
    public function getProductById($id) {
        $product = null;
        
        try {
            $product = $this->productRepository->getById($id);
        } catch(\Exception $e) {
            $product = null;
        }
        
        return $product;
    }
    
    public function getProductBySku($sku) {
        $product = null;
        
        try {
            $product = $this->productRepository->get($sku);
        } catch(\Exception $e) {
            $product = null;
        }
        
        return $product;
    }
    
    public function getOrderFromQueue($order_id) {
        $collection = $this->_objectManager->get('Toppik\Sap\Model\ResourceModel\OrderQueue\CollectionFactory')->create();
        $collection->setOrderFilter($order_id);
        return $collection->getFirstItem();
    }
    
    public function getItemsFromQueue($order_id) {
        $collection = $this->_objectManager->get('Toppik\Sap\Model\ResourceModel\OrderItem\CollectionFactory')->create();
        $collection->setOrderFilter($order_id);
        return $collection->getItems();
    }
    
}
