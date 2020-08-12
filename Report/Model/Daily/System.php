<?php
namespace Toppik\Report\Model\Daily;

use Magento\Framework\App\ResourceConnection;

class System extends \Magento\Framework\Model\AbstractModel {
	
    /**
     * @var ObjectManagerInterface
     */
    private $objectManager;
	
    /**
     * @var ResourceConnection
     */
    private $resource;
	
    /**
     * @var \Magento\Framework\Stdlib\DateTime\DateTime
     */
    private $dateTime;
	
    /**
     * @param Action\Context $context
     * @param PageFactory $resultPageFactory
     */
    public function __construct(
        \Magento\Framework\ObjectManagerInterface $objectManager,
        ResourceConnection $resource,
		\Magento\Framework\Stdlib\DateTime\DateTime $dateTime
    ) {
        $this->objectManager = $objectManager;
        $this->resource = $resource;
        $this->dateTime = $dateTime;
    }
	
	public function getPendingQueue() {
		$collection = array();
		
		$data = $this->resource->getConnection(ResourceConnection::DEFAULT_CONNECTION)->fetchAll(
			sprintf(
				'SELECT id, `created_at` AS Date, `entity_type` AS "Entity Type", `entity_id` AS "Entity ID", `message` AS Message, `amount` AS Amount, `customer_id` AS "Customer ID", `customer_name` AS "Customer Name", `customer_email` AS "Customer Email", `customer_phone` AS "Customer Phone"
				FROM %s
				WHERE email_sent = 0',
				$this->resource->getTableName('toppikreport_daily_system')
			)
		);
		
		if(count($data)) {
			foreach($data as $_item) {
				$collection[$_item['id']] = $_item;
			}
		}
		
		return $collection;
	}
	
	public function addRecord(
		$entity_type = null,
		$entity_id = null,
		$message = null,
		$amount = null,
		$customer_id = null,
		$customer_name = null,
		$customer_email = null,
		$customer_phone = null
	) {
		try {
			if($message) {
                $connection = $this->resource->getConnection(\Magento\Framework\App\ResourceConnection::DEFAULT_CONNECTION);
				$values = array();
				
				$values[] = sprintf(
					'(%s, %s, %s, %s, %s, %s, %s, %s, %s)',
                    $connection->quote($this->dateTime->gmtDate('Y-m-d H:i:s')),
                    $connection->quote($entity_type),
                    $connection->quote($entity_id),
                    $connection->quote($message),
                    $connection->quote($amount),
                    $connection->quote($customer_id),
                    $connection->quote($customer_name),
                    $connection->quote($customer_email),
                    $connection->quote($customer_phone)
				);
				
				$connection->query(
					sprintf(
						'INSERT INTO %s (
							created_at,
							entity_type,
							entity_id,
							message,
							amount,
							customer_id,
							customer_name,
							customer_email,
							customer_phone
						) VALUES %s',
						$this->resource->getTableName('toppikreport_daily_system'),
						implode(',', $values)
					)
				);
			}
		} catch(\Exception $e) {
			
		}
	}
	
	public function updateById($ids = array()) {
		if($ids && count($ids)) {
            $connection = $this->resource->getConnection(\Magento\Framework\App\ResourceConnection::DEFAULT_CONNECTION);
            
			$connection->query(
				sprintf(
					'UPDATE %s SET email_sent = 1 WHERE %s',
					$this->resource->getTableName('toppikreport_daily_system'),
                    $connection->quoteInto('id IN(?)', $ids)
				)
			);
		}
	}
	
}
