<?php
namespace Toppik\Report\Controller\Debug;

use Magento\Framework\App\Action;
use Magento\Framework\App\Action\Context;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Controller\ResultInterface;

class Analytic extends Action\Action {
	
    /**
     * @var ManagerInterface
     */
    private $eventManager;
	
    /**
     * @param Action\Context $context
     * @param PageFactory $resultPageFactory
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Framework\Event\ManagerInterface $eventManager
    ) {
        $this->eventManager = $eventManager;
        parent::__construct($context);
    }
	
    public function execute() {
        if($this->getRequest()->getParam('_t_')) {
            $model = $this->_objectManager->get('Toppik\Analytic\Model\ResourceModel\Visitor');
            
            $collection = $this->getPendingQueue();
            
            $rows 		= $this->_generateRows($collection, array('Date', 'Timezone', 'Visitor ID', 'Customer ID', 'Order ID', 'Referrer', 'Request URL', 'Session ID', 'Cookie ID', 'IP'));
            $csv 		= $this->_generateFile($rows, array('Date', 'Timezone', 'Visitor ID', 'Customer ID', 'Order ID', 'Referrer', 'Request URL', 'Session ID', 'Cookie ID', 'IP'));
            
            echo '<pre>';
            echo $csv;
            exit;
        }
        
		$resultRedirect = $this->resultRedirectFactory->create();
		return $resultRedirect->setPath('/');
    }
	
	public function getPendingQueue() {
		$collection = array();
        
        $localeDate = $this->_objectManager->get('Magento\Framework\Stdlib\DateTime\TimezoneInterface');
        $resource   = $this->_objectManager->get('Magento\Framework\App\ResourceConnection');
        
        $dateTime = new \DateTime();
        $dateTime->setTimeZone(new \DateTimeZone($localeDate->getConfigTimezone()));
        $timezone = $dateTime->format('T');
        
		$data = $resource->getConnection(\Magento\Framework\App\ResourceConnection::DEFAULT_CONNECTION)->fetchAll(
			sprintf(
				'SELECT main_table.id, main_table.`created_at` AS Date, "%s" AS "Timezone", main_table.`visitor_id` AS "Visitor ID", main_table.`customer_id` AS "Customer ID", main_table.`order_id` AS "Order ID", main_table.`obtoua` AS "OBTOUA", main_table.`request_url` AS "Request URL", main_table.`referrer` AS "Referrer", main_table.`s_id` AS "Session ID", main_table.`c_id` AS "Cookie ID", INET_NTOA(main_table.`ip`) AS IP
				FROM %s AS main_table
				WHERE main_table.email_sent = 0
                ORDER BY main_table.`visitor_id` ASC, main_table.id ASC',
                $timezone,
				$resource->getTableName(\Toppik\Analytic\Model\ResourceModel\Visitor::MAIN_TABLE)
			)
		);
		
		if(count($data)) {
			foreach($data as $_item) {
                if(isset($_item['Date'])) {
                    $date 	= $localeDate->date(new \DateTime($_item['Date']));
                    $_item['Date'] = $date->format('Y-m-d H:i:s');
                }
                
				$collection[$_item['id']] = $_item;
			}
		}
		
		return $collection;
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
