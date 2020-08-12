<?php
namespace Toppik\Report\Observer;

class SystemAddMessage implements \Magento\Framework\Event\ObserverInterface {
	
    /**
     * @var \Toppik\Report\Model\Daily\System
     */
    private $model;
	
    /**
     * SystemAddMessage constructor.
     * @param \Toppik\Report\Model\Daily\System $model
     */
    public function __construct(
		\Toppik\Report\Model\Daily\System $model
    ) {
        $this->model = $model;
    }
	
    /**
     * @param Observer $observer
     * @return void
     */
    public function execute(\Magento\Framework\Event\Observer $observer) {
		$entity_type 	= $observer->getEntityType();
		$entity_id 		= $observer->getEntityId();
		$message 		= $observer->getMessage();
		$amount 		= $observer->getAmount();
		$customer_id 	= $observer->getCustomerId();
		$customer_name 	= $observer->getCustomerName();
		$customer_email = $observer->getCustomerEmail();
		$customer_phone = $observer->getCustomerPhone();
		
		$this->model->addRecord($entity_type, $entity_id, $message, $amount, $customer_id, $customer_name, $customer_email, $customer_phone);
    }
	
}
