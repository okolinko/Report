<?php
namespace Toppik\Report\Controller\Debug;

use Magento\Framework\App\Action;
use Magento\Framework\App\Action\Context;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Controller\ResultInterface;

class Reports extends Action\Action {
	
    /**
     * @var ResourceConnection
     */
    protected $_resource;
	
    /**
     * @var \Magento\Framework\App\Response\Http\FileFactory
     */
    protected $_fileFactory;
	
    /**
     * @param Action\Context $context
     * @param PageFactory $resultPageFactory
     */
    public function __construct(
        Action\Context $context,
        \Magento\Framework\App\ResourceConnection $resource,
        \Magento\Framework\App\Response\Http\FileFactory $fileFactory
    ) {
        $this->_resource = $resource;
        $this->_fileFactory = $fileFactory;
        parent::__construct($context);
    }
	
    public function execute() {
        $orderTable = $this->_resource->getTableName('sales_order');
        $connection = $this->_resource->getConnection(\Magento\Framework\App\ResourceConnection::DEFAULT_CONNECTION);
		
        $collection = $connection->fetchAll('
			SELECT GROUP_CONCAT(DISTINCT p.`profile_id`) AS profile_id, GROUP_CONCAT(DISTINCT p.`created_at`) AS created_at, GROUP_CONCAT(DISTINCT p.`status`) AS status, GROUP_CONCAT(DISTINCT p.`grand_total`) AS subscription_total, GROUP_CONCAT(DISTINCT p.`frequency_title`) AS frequency, GROUP_CONCAT(DISTINCT p.`customer_id`) AS customer_id, GROUP_CONCAT(DISTINCT c.firstname) AS customer_firstname, GROUP_CONCAT(DISTINCT c.lastname) AS customer_lastname, GROUP_CONCAT(DISTINCT c.email) AS customer_email, COUNT(po.order_id) AS order_count, SUM(o.grand_total) AS order_total
			FROM `subscriptions_profiles` AS p
			INNER JOIN customer_entity AS c ON c.entity_id = p.customer_id
			INNER JOIN subscriptions_profiles_orders AS po ON po.profile_id = p.profile_id
			INNER JOIN sales_order AS o ON o.entity_id = po.order_id
			WHERE p.created_at BETWEEN "2015-11-01 00:00:00" AND "2016-01-01 00:00:00"
			GROUP BY p.profile_id
		');
		
        $headers 	= array('profile_id', 'created_at', 'status', 'subscription_total', 'frequency', 'customer_id', 'customer_firstname', 'customer_lastname', 'customer_email', 'order_count', 'order_total');
		$rows 		= $this->_generateRows($collection, $headers);
		$csv 		= $this->_generateFile($rows, $headers);
		
        return $this->_fileFactory->create('duplicate-by-quote.csv', $csv, \Magento\Framework\App\Filesystem\DirectoryList::VAR_DIR);
    }
	
	protected function _generateRows($collection, $headers) {
		$rows = array();
		
        foreach($collection as $_item) {
			$values = array();
			
			foreach($headers as $_header) {
				$values[$_header] = isset($_item[$_header]) ? $_item[$_header] : '';
			}
			
			$rows[] = $values;
        }
		
		return $rows;
	}
	
    protected function _generateFile($rows, $headers) {
        $fd = fopen('php://temp/maxmemory:'.(1024 * 1024 * 10)/*10MB*/, 'w');
		
        fputcsv($fd, $headers);
		
        foreach($rows as $row) {
			$values = array();
			
			foreach($headers as $_header) {
				$values[] = isset($row[$_header]) ? $row[$_header] : '';
			}
			
            fputcsv($fd, $values);
        }
		
        rewind($fd);
		
        $csv = stream_get_contents($fd);
		
        fclose($fd);
		
        return $csv;
    }
	
}
