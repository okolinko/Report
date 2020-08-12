<?php
namespace Toppik\Report\Model\Subscription;

class MonthlySum extends \Magento\Framework\Model\AbstractModel {
	
	protected $_months = 48;
    
    /**
     * @var ObjectManagerInterface
     */
    private $objectManager;
	
    /**
     * @var \Magento\Framework\App\ResourceConnection
     */
    private $resource;
	
    /**
     * @param Action\Context $context
     * @param PageFactory $resultPageFactory
     */
    public function __construct(
        \Magento\Framework\ObjectManagerInterface $objectManager,
        \Magento\Framework\App\ResourceConnection $resource
    ) {
        $this->objectManager = $objectManager;
        $this->resource = $resource;
    }
	
    public function refresh() {
		$data = array();
		$last = null;
        
		for($i = 0; $i < $this->_months; $i++) {
			$date = new \DateTime();
			$date->sub(new \DateInterval(sprintf('P%sM', $i)));
            
			$days 	= cal_days_in_month(CAL_GREGORIAN, $date->format('m'), $date->format('Y'));
			$diff   = $days - $date->format('d');
            
            if($diff > 0 && $diff < $days) {
                $date->add(new \DateInterval(sprintf('P%sD', $diff)));
            }
            
            $date->setTime(23, 59, 59);
            
            $activeToppik   = $this->getActive($date, false);
            $activeMs       = $this->getActive($date, true);
            
            if($last === null || $last != $date->format('Y')) {
                $last = $date->format('Y');
                
                $data[] = array(
                    'period'            => $date->format('Y'),
                    'count_active_toppik'   => '',
                    'sum_active_toppik'     => '',
                    'count_active_ms'       => '',
                    'sum_active_ms'         => '',
                    'count_active'          => '',
                    'sum_active'            => ''
                );
            }
            
            $data[] = array(
                'period'                => $date->format('Y-m'),
                
                'count_active_toppik'   => $activeToppik['count'],
                'sum_active_toppik'     => $activeToppik['sum'],
                
                'count_active_ms'       => $activeMs['count'],
                'sum_active_ms'         => $activeMs['sum'],
                
                'count_active'          => ($activeToppik['count'] + $activeMs['count']),
                'sum_active'            => ($activeToppik['sum'] + $activeMs['sum'])
            );
		}
        
        $this->_save($data);
    }
	
	public function getActive($date, $ms = false) {
        $collection = array();
        $count      = 0;
        $sum        = 0;
        
        $from       = new \DateTime(sprintf('%s-%s-01 00:00:00', $date->format('Y'), $date->format('m')));
        
        /* Convert to store datetime */
        $from->add(new \DateInterval("PT8H"));
        
        $to = clone $date;
        
        /* Convert to store datetime */
        $to->add(new \DateInterval("PT8H"));
        
        $data = $this->resource->getConnection(\Magento\Framework\App\ResourceConnection::DEFAULT_CONNECTION)->fetchRow(
			sprintf(
				'
                    SELECT
                        COUNT(profile_id) AS count,
                        SUM(grand_total) AS sum
                    FROM %s AS main_table
                    WHERE
                        merchant_source %s "%s"
                        AND created_at <= "%s"
                        AND
                            (
                                status = "%s"
                                ||
                                    (
                                        status = "%s"
                                        AND cancelled_at IS NOT NULL
                                        AND cancelled_at > "%s"
                                    )
                                ||
                                    (
                                        status = "%s"
                                        AND suspended_at IS NOT NULL
                                        AND suspended_at > "%s"
                                    )
                            )
                ',
				$this->resource->getTableName('subscriptions_profiles'),
                
                ($ms === false ? '=' : '!='),
                \Toppik\OrderSource\Model\Merchant\Source::SOURCE_0,
                $to->format('Y-m-d H:i:s'),
                
                \Toppik\Subscriptions\Model\Profile::STATUS_ACTIVE,
                
                \Toppik\Subscriptions\Model\Profile::STATUS_CANCELLED,
                $from->format('Y-m-d H:i:s'),
                
                \Toppik\Subscriptions\Model\Profile::STATUS_SUSPENDED,
                $from->format('Y-m-d H:i:s')
			)
		);
        
        if(is_array($data)) {
            $count  = isset($data['count']) ? (int) $data['count'] : 0;
            $sum    = isset($data['sum']) ? (float) $data['sum'] : 0;
        }
        
        $collection['count'] = $count;
        $collection['sum'] = $sum;
        
        return $collection;
	}
    
    protected function _save($data = array()) {
        $this->resource->getConnection(\Magento\Framework\App\ResourceConnection::DEFAULT_CONNECTION)->query(
            sprintf(
                'TRUNCATE %s',
                $this->resource->getTableName('subscription_report_monthly_sum')
            )
        );
        
        if(count($data)) {
            $values = array();
            
            foreach($data as $_data) {
                $values[] = vsprintf('("%s", "%s", "%s", "%s", "%s", "%s", "%s")', $_data);
            }
            
            $this->resource->getConnection(\Magento\Framework\App\ResourceConnection::DEFAULT_CONNECTION)->query(
                sprintf(
                    'INSERT INTO %s (
                        period,
                        count_active_toppik,
                        sum_active_toppik,
                        count_active_ms,
                        sum_active_ms,
                        count_active,
                        sum_active
                    ) VALUES %s',
                    $this->resource->getTableName('subscription_report_monthly_sum'),
                    implode(',', $values)
                )
            );
        }
    }
    
}
