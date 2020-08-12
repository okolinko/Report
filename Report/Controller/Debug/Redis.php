<?php
namespace Toppik\Report\Controller\Debug;

class Redis extends \Magento\Framework\App\Action\Action {
	
    /**
     * @var ManagerInterface
     */
    private $eventManager;
	
    /**
     * @var ResourceConnection
     */
    private $resource;
    
    /**
     * @var \Toppik\Subscriptions\Helper\Report
     */
    private $reportHelper;
	
    protected $customerRepositoryInterface;
    
    /**
     * @param Action\Context $context
     * @param PageFactory $resultPageFactory
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Framework\Event\ManagerInterface $eventManager,
        \Magento\Framework\App\ResourceConnection $resource,
		\Toppik\Subscriptions\Helper\Report $reportHelper,
        \Magento\Customer\Api\CustomerRepositoryInterface $customerRepositoryInterface
    ) {
        $this->eventManager = $eventManager;
        $this->resource = $resource;
		$this->reportHelper = $reportHelper;
		$this->customerRepositoryInterface = $customerRepositoryInterface;
        parent::__construct($context);
    }
	
    public function execute() {
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        
        $profile    = $objectManager->get('Toppik\Subscriptions\Model\Profile');
        
        $profile->load(58510);
        
        try {
            $this->reportHelper->sendSuspendNotifications($profile);
        } catch(\Exception $e) {
            echo $e->getMessage();
        }
        
    }
    
}
