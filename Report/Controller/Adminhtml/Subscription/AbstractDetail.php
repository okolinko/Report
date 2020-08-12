<?php
namespace Toppik\Report\Controller\Adminhtml\Subscription;

abstract class AbstractDetail extends \Magento\Backend\App\Action {
	
    protected $_allowedParams = array();
    
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory
    ) {
		parent::__construct($context);
        $this->resultPageFactory = $resultPageFactory;
    }
	
    protected function _initParams() {
        $data   = array();
        $params = $this->getRequest()->getParams();
        
        if(is_array($params) && count($params) > 0) {
            foreach($this->_allowedParams as $_param) {
                if(isset($params[$_param])) {
                    $value = urldecode($params[$_param]);
                    
                    if($_param == 'subscription_period') {
                        $value = $value / (60 * 60 * 24);
                    } else if(strpos($_param, '_at') !== false) {
                        $_date = new \DateTime($value);
                        $_date->sub(new \DateInterval("PT7H"));
                        $value = $_date->format('Y-m-d H:i:s');
                    }
                    
                    $data[] = $value;
                }
            }
        }
        
        return $data;
    }
	
    /**
     * @return $this
     */
    protected function _initAction() {
        $resultPage = $this->resultPageFactory->create();
        return $resultPage;
    }
	
}
