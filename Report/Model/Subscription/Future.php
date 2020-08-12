<?php
namespace Toppik\Report\Model\Subscription;

use Magento\Framework\App\ResourceConnection;

class Future extends \Magento\Framework\Model\AbstractModel {
    
    protected $_days = 365;
    
    /**
     * @var ObjectManagerInterface
     */
    private $objectManager;
	
    /**
     * @var ResourceConnection
     */
    private $resource;
	
    /**
     * @param Action\Context $context
     * @param PageFactory $resultPageFactory
     */
    public function __construct(
        \Magento\Framework\ObjectManagerInterface $objectManager,
        ResourceConnection $resource
    ) {
        $this->objectManager = $objectManager;
        $this->resource = $resource;
    }
	
    public function refresh() {
        $data           = array();
        $subscriptions  = $this->getActiveSubscriptions();
        
        if(count($subscriptions) > 0) {
            foreach($subscriptions as $_item) {
                $profile_id         = isset($_item['profile_id']) ? $_item['profile_id'] : null;
                $grand_total        = isset($_item['grand_total']) ? $_item['grand_total'] : null;
                $frequency_length   = isset($_item['frequency_length']) ? $_item['frequency_length'] : null;
                $merchant_source    = isset($_item['merchant_source']) ? $_item['merchant_source'] : null;
                $next_order_at      = isset($_item['next_order_at']) ? $_item['next_order_at'] : null;
                
                if($frequency_length > 0) {
                    $from   = new \DateTime((new \DateTime($next_order_at))->sub(new \DateInterval("PT7H"))->format('Y-m-d H:i:s'));
                    $to     = new \DateTime();
                    
                    $to->add(new \DateInterval(sprintf('P%sD', $this->_days)));
                    
                    $from->setTime(0, 0, 0);
                    $to->setTime(0, 0, 0);
                    
                    // echo sprintf('%s - %s', $from->format('Y-m-d H:i:s'), $to->format('Y-m-d H:i:s'));exit;
                    
                    if($from < $to) {
                        do {
                            $data[] = array($from->format('Y-m-d'), $profile_id, $frequency_length, $grand_total, $merchant_source);
                            $from->add(new \DateInterval(sprintf('P%sD', $frequency_length)));
                        } while($from <= $to);
                    }
                }
            }
        }
        
        $this->_save($data);
        $this->_aggregate();
    }
	
	public function getActiveSubscriptions() {
		$collection = array();
		
		$data = $this->resource->getConnection(\Magento\Framework\App\ResourceConnection::DEFAULT_CONNECTION)->fetchAll(
			sprintf(
				'SELECT profile_id, grand_total, (FLOOR(frequency_length / (60 * 60 * 24))) AS frequency_length, merchant_source, next_order_at
                FROM %s AS main_table
                WHERE status = "%s"',
				$this->resource->getTableName('subscriptions_profiles'),
                \Toppik\Subscriptions\Model\Profile::STATUS_ACTIVE
			)
		);
		
		if(count($data)) {
			foreach($data as $_item) {
				$collection[] = $_item;
			}
		}
		
		return $collection;
	}
    
    protected function _save($data = array()) {
        $this->resource->getConnection(\Magento\Framework\App\ResourceConnection::DEFAULT_CONNECTION)->query(
            sprintf(
                'TRUNCATE %s',
                $this->resource->getTableName('subscription_report_future')
            )
        );
        
        if(count($data)) {
            $values = array();
            
            foreach($data as $_data) {
                $values[] = vsprintf('("%s", "%s", "%s", "%s", "%s")', $_data);
            }
            
            $this->resource->getConnection(\Magento\Framework\App\ResourceConnection::DEFAULT_CONNECTION)->query(
                sprintf(
                    'INSERT INTO %s (
                        date,
                        subscription_id,
                        subscription_period,
                        subscription_total,
                        subscription_merchant_source
                    ) VALUES %s',
                    $this->resource->getTableName('subscription_report_future'),
                    implode(',', $values)
                )
            );
        }
    }
    
    protected function _aggregate() {
        $data   = array();
        $items  = $this->_getQueue();
        
        if(count($items) > 0) {
            foreach($items as $_item) {
                $date                           = isset($_item['date']) ? $_item['date'] : null;
                $subscription_id                = isset($_item['subscription_id']) ? $_item['subscription_id'] : null;
                $subscription_total             = isset($_item['subscription_total']) ? $_item['subscription_total'] : null;
                $subscription_merchant_source   = isset($_item['subscription_merchant_source']) ? $_item['subscription_merchant_source'] : null;
                
                if($date && $subscription_id && $subscription_total && $subscription_merchant_source) {
                    if(!isset($data[$date])) {
                        $data[$date] = array(
                                            'date'                      => $date,
                                            'subscription_count_toppik' => 0,
                                            'subscription_total_toppik' => 0,
                                            'subscription_count_ms'     => 0,
                                            'subscription_total_ms'     => 0
                                        );
                    }
                    
                    $_data = $data[$date];
                    
                    if($subscription_merchant_source == \Toppik\OrderSource\Model\Merchant\Source::SOURCE_0) {
                        $_data['subscription_count_toppik'] = $_data['subscription_count_toppik'] + 1;
                        $_data['subscription_total_toppik'] = $_data['subscription_total_toppik'] + $subscription_total;
                    } else {
                        $_data['subscription_count_ms'] = $_data['subscription_count_ms'] + 1;
                        $_data['subscription_total_ms'] = $_data['subscription_total_ms'] + $subscription_total;
                    }
                    
                    $data[$date] = $_data;
                }
            }
        }
        
        $this->_saveAggregated($data);
    }
    
	protected function _getQueue() {
		$collection = array();
		
		$data = $this->resource->getConnection(\Magento\Framework\App\ResourceConnection::DEFAULT_CONNECTION)->fetchAll(
			sprintf(
				'SELECT * FROM %s AS main_table',
				$this->resource->getTableName('subscription_report_future')
			)
		);
		
		if(count($data)) {
			foreach($data as $_item) {
				$collection[] = $_item;
			}
		}
		
		return $collection;
	}
    
    protected function _saveAggregated($data = array()) {
        $this->resource->getConnection(\Magento\Framework\App\ResourceConnection::DEFAULT_CONNECTION)->query(
            sprintf(
                'TRUNCATE %s',
                $this->resource->getTableName('subscription_report_future_aggregated')
            )
        );
        
        if(count($data)) {
            $values = array();
            
            foreach($data as $_data) {
                $values[] = vsprintf('("%s", "%s", "%s", "%s", "%s")', $_data);
            }
            
            $this->resource->getConnection(\Magento\Framework\App\ResourceConnection::DEFAULT_CONNECTION)->query(
                sprintf(
                    'INSERT INTO %s (
                        date,
                        subscription_count_toppik,
                        subscription_total_toppik,
                        subscription_count_ms,
                        subscription_total_ms
                    ) VALUES %s',
                    $this->resource->getTableName('subscription_report_future_aggregated'),
                    implode(',', $values)
                )
            );
        }
    }
    
}
