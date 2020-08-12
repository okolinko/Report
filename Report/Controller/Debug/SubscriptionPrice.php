<?php
namespace Toppik\Report\Controller\Debug;

use Magento\Framework\App\Action;
use Magento\Framework\App\Action\Context;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Controller\ResultInterface;

class SubscriptionPrice extends Action\Action {
    
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
        \Magento\Store\Model\StoreManagerInterface $storeManager
    ) {
		$this->subscriptionHelper = $subscriptionHelper;
        $this->eventManager = $eventManager;
        $this->resource = $resource;
        $this->orderFactory = $orderFactory;
        $this->productRepository = $productRepository;
        $this->_storeManager = $storeManager;
        parent::__construct($context);
    }
	
    public function execute() {
        $update     = (int) $this->getRequest()->getParam('update');
        $from       = (int) $this->getRequest()->getParam('from');
        $to         = (int) $this->getRequest()->getParam('to');
        
        $messages   = array();
        $updated    = 0;
        $failed     = 0;
        
        $messages[] = sprintf('Start subscription_price in "%s" mode with range %s-%s', ($update === 1 ? 'LIVE' : 'TEST'), $from, $to);
        
        $collection = $this->_getCollection($from, $to);
        
        $messages[] = sprintf('Found %s item(s)', count($collection->getItems()));
        $messages[] = sprintf('Sql: %s', (string) $collection->getSelect());
        
        foreach($collection->getItems() as $_profile) {
            try {
                $this->_storeManager->setCurrentStore($_profile->getStoreId());
                
                foreach($_profile->getAllVisibleItems() as $_item) {
                    if((int) $_item->getData('is_onetime_gift') !== 1) {
                        $product = $this->_getProductById($_item->getProductId(), $_profile->getStoreId());
                        
                        if(!$product || !$product->getId()) {
                            throw new \Exception(sprintf('Product with ID %s not found', $_item->getProductId()));
                        }
                        
                        $subscription_item = $this->_getSubscriptionItem($_profile);
                        
                        if(!$subscription_item || !$subscription_item->getId()) {
                            throw new \Exception(sprintf('Subscription item with length %s not found', $_profile->getFrequencyLength()));
                        }
                        
                        if($_item->getPrice() != $subscription_item->getRegularPrice()) {
                            
                            $mleft = $subscription_item->getRegularPrice();
                            $mright = $_item->getTaxAmount();
                            $precision = 2;
                            $mresult = bcmul($mleft, $mright, $precision);
                            $dresult = bcdiv($mresult, $_item->getPrice(), $precision);
                            $tax = $dresult;
                            //$tax = number_format($dresult, $precision); //remove later
                            $messages[] = sprintf(
                                'Found item with ID %s in profile ID %s to update price: old price - %s, new price - %s, old tax - %s, new tax - %s',
                                $_item->getId(),
                                $_profile->getId(),
                                $_item->getPrice(),
                                $subscription_item->getRegularPrice(),
                                $_item->getTaxAmount(),
                                $tax
                            );
                            
                            if($update === 1) {
                                $message = sprintf(
                                    'Updated item price with ID %s in profile ID %s: old price - %s, new price - %s, old tax - %s, new tax - %s',
                                    $_item->getId(),
                                    $_profile->getId(),
                                    $_item->getPrice(),
                                    $subscription_item->getRegularPrice(),
                                    $_item->getTaxAmount(),
                                    $tax
                                );
                                
                                $_item
                                    ->setData('price', $subscription_item->getRegularPrice())
                                    ->setData('base_price', $subscription_item->getRegularPrice())
                                    ->setData('custom_price', $subscription_item->getRegularPrice())
                                    ->setData('original_custom_price', $subscription_item->getRegularPrice())
                                    ->setData('tax_amount', $tax)
                                    ->setData('base_tax_amount', $tax)
                                    ->setData('row_total', $subscription_item->getRegularPrice() * $_item->getQty())
                                    ->setData('base_row_total', $subscription_item->getRegularPrice() * $_item->getQty())
                                    ->setData('price_incl_tax', $subscription_item->getRegularPrice() + $tax)
                                    ->setData('base_price_incl_tax', $subscription_item->getRegularPrice() + $tax)
                                    ->setData('row_total_incl_tax', (($subscription_item->getRegularPrice() * $_item->getQty()) + $tax))
                                    ->setData('base_row_total_incl_tax', (($subscription_item->getRegularPrice() * $_item->getQty()) + $tax))
                                    ->save();
                                
                                $grand_total        = 0;
                                $base_ground_total  = 0;
                                $items_count        = 0;
                                $items_qty          = 0;
                                
                                foreach($_profile->getAllVisibleItems() as $_item) {
                                    $grand_total        = $grand_total + $_item->getData('row_total');
                                    $base_ground_total  = $base_ground_total + $_item->getData('base_row_total');
                                    $items_count        = $items_count + 1;
                                    $items_qty          = $items_qty + $_item->getQty();
                                }
                                
                                $_profile->setGrandTotal($grand_total);
                                $_profile->setBaseGrandTotal($base_ground_total);
                                $_profile->setItemsCount($items_count);
                                $_profile->setItemsQty($items_qty);
                                
                                $_profile->setStatusHistoryCode('product_price');
                                $_profile->setStatusHistoryMessage($message);
                                $_profile->save();
                                
                                $updated++;
                                $messages[] = $message;
                                $messages[] = sprintf('Saved profile ID %s', $_profile->getId());
                            }
                        }
                    }
                }
            } catch(\Exception $e) {
                $failed++;
				$messages[] = sprintf('ERROR during processing profile ID %s: %s', $_profile->getId(), $e->getMessage());
            }
        }
        
        $messages[] = sprintf('End subscription_price in "%s" mode: %s updated, %s failed', ($update === 1 ? 'LIVE' : 'TEST'), $updated, $failed);
        
        echo '<pre>';print_r($messages);exit;
    }
    
    protected function _getCollection($from, $to) {
        $collection = $this->_objectManager->create('\Toppik\Subscriptions\Model\ResourceModel\Profile\Collection');
		
        $collection
            ->addFieldToFilter(
                \Toppik\Subscriptions\Model\Profile::STATUS,
                array(
                    'in' => array(
                        \Toppik\Subscriptions\Model\Profile::STATUS_ACTIVE,
                        \Toppik\Subscriptions\Model\Profile::STATUS_SUSPENDED,
                        \Toppik\Subscriptions\Model\Profile::STATUS_SUSPENDED_TEMPORARILY
                    )
                )
            )
            ->addFieldToFilter(
                \Toppik\Subscriptions\Model\Profile::PROFILE_ID,
                [
                    ['gteq' => $from]
                ]
            )
            ->addFieldToFilter(
                \Toppik\Subscriptions\Model\Profile::PROFILE_ID,
                [
                    ['lteq' => $to]
                ]
            );
        
        return $collection;
    }
    
	protected function _getSubscriptionItem($profile) {
        $item = null;
        
        $subscription = $this->subscriptionHelper->getSubscriptionByProduct($profile->getSubscriptionProduct());
        
        echo sprintf('<div style="display: none;" class="debug">%s: ID "%s", type "%s"</div>', $profile->getId(), is_object($profile->getSubscriptionProduct()) ? $profile->getSubscriptionProduct()->getId() : '', gettype($subscription));
        
        if($subscription) {
            foreach($subscription->getItemsCollection() as $_item) {
                
                echo sprintf('<div style="display: none;" class="debug">%s: %s == %s</div>', $profile->getId(), $profile->getFrequencyLength(), ($_item->getPeriod()->getLength() * $_item->getUnit()->getLength()));
                
                if($profile->getFrequencyLength() == ($_item->getPeriod()->getLength() * $_item->getUnit()->getLength())) {
                    $item = $_item;
                    break;
                }
            }
        }
        
        return $item;
	}
    
    protected function _getProductById($id, $store_id) {
        $product = null;
        
        try {
            $product = $this->productRepository->getById($id, false, $store_id);
        } catch(\Exception $e) {
            $product = null;
        }
        
        return $product;
    }
    
    protected function _getProductBySku($sku, $store_id) {
        $product = null;
        
        try {
            $product = $this->productRepository->get($sku, false, $store_id);
        } catch(\Exception $e) {
            $product = null;
        }
        
        return $product;
    }
    
}
