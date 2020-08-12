<?php
namespace Toppik\Report\Controller\Debug;

use Magento\Framework\App\Action;
use Magento\Framework\App\Action\Context;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Controller\ResultInterface;

class Import extends Action\Action {
	
    /**
     * @var ManagerInterface
     */
    private $eventManager;
    
    protected $_productFactory;
    protected $_productResourceModel;
    
    /**
     * @param Action\Context $context
     * @param PageFactory $resultPageFactory
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Framework\Event\ManagerInterface $eventManager,
        \Magento\Catalog\Model\ProductFactory $productFactory,
        \Magento\Catalog\Model\ResourceModel\Product $productResourceModel
    ) {
        $this->eventManager = $eventManager;
        $this->_productFactory = $productFactory;
        $this->_productResourceModel = $productResourceModel;
        parent::__construct($context);
    }
	
    public function execute() {
        try {
            $messages = array();
            $products = $this->_getProducts();
            
            $messages[] = sprintf(
                'Found %s product(s)',
                count($products)
            );
            
            foreach($products as $_product) {
                try {
                    if($_product->getTypeId() == 'configurable') {
                        $children = $_product->getTypeInstance()->getUsedProducts($_product);
                        
                        if(empty($children)) {
                            throw new \Exception(
                                sprintf(
                                    'Configurable product ID %s and sku %s does not have simple products',
                                    $_product->getId(),
                                    $_product->getSku()
                                )
                            );
                        }
                        
                        if($_product->getData('price') < 0.001) {
                            throw new \Exception(
                                sprintf(
                                    'Configurable product ID %s and sku %s does not have price (%s)',
                                    $_product->getId(),
                                    $_product->getSku(),
                                    $_product->getData('price')
                                )
                            );
                        }
                        
                        foreach($children as $_child) {
                            $price = $_child->getData('price');
                            
                            if($_product->getData('price') != $_child->getData('price')) {
                                $_child->setPrice($_product->getData('price'));
                                $this->_productResourceModel->saveAttribute($_child, 'price');
                                
                                $messages[] = sprintf(
                                    '200OK -> Updated simple product ID %s with sku %s of configurable ID %s and sku %s to price %s (was %s)',
                                    $_child->getId(),
                                    $_child->getSku(),
                                    $_product->getId(),
                                    $_product->getSku(),
                                    $_product->getData('price'),
                                    $price
                                );
                            } else {
                                $messages[] = sprintf(
                                    '302OK -> Simple product ID %s with sku %s of configurable ID %s and sku %s has the same price as configurable (%s)',
                                    $_child->getId(),
                                    $_child->getSku(),
                                    $_product->getId(),
                                    $_product->getSku(),
                                    $price
                                );
                            }
                        }
                    }
                } catch(\Exception $e) {
                    $messages[] = $e->getMessage();
                }
            }
        } catch(\Exception $e) {
            $messages[] = $e->getMessage();
        }
        
        echo '<pre>';print_r($messages);exit;
        
    }
    
    protected function _getProducts() {
        return $this->_productFactory->create()
                        ->getCollection()
                        ->addAttributeToSelect(array('price'))
                        ->addAttributeToFilter('type_id', array('in' => 'configurable'));
    }
    
}
