<?php
namespace Toppik\Report\Model\Subscription;

class Pivot extends \Magento\Framework\Model\AbstractModel {
	
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
            
            $active             = $this->getActive($date);
            $suspended          = $this->getSuspended($date);
            $cancelled          = $this->getCancelled($date);
            $new_subscriptions  = $this->getNewSubscriptions($date);
            $suspended_month    = $this->getSuspendedMonth($date);
            $cancelled_month    = $this->getCancelledMonth($date);
            
            if($last === null || $last != $date->format('Y')) {
                $last = $date->format('Y');
                
                $data[] = array(
                    'period'                => $date->format('Y'),
                    'count_active'          => '',
                    'sum_active'            => '',
                    'count_suspended'       => '',
                    'sum_suspended'         => '',
                    'count_cancelled'       => '',
                    'sum_cancelled'         => '',
                    'count_new_month'       => '',
                    'sum_new_month'         => '',
                    'count_suspended_month' => '',
                    'sum_suspended_month'   => '',
                    'count_cancelled_month' => '',
                    'sum_cancelled_month'   => ''
                );
            }
            
            $data[] = array(
                'period'                => $date->format('Y-m'),
                'count_active'          => $active['count'],
                'sum_active'            => $active['sum'],
                'count_suspended'       => $suspended['count'],
                'sum_suspended'         => $suspended['sum'],
                'count_cancelled'       => $cancelled['count'],
                'sum_cancelled'         => $cancelled['sum'],
                'count_new_month'       => $new_subscriptions['count'],
                'sum_new_month'         => $new_subscriptions['sum'],
                'count_suspended_month' => $suspended_month['count'],
                'sum_suspended_month'   => $suspended_month['sum'],
                'count_cancelled_month' => $cancelled_month['count'],
                'sum_cancelled_month'   => $cancelled_month['sum']
            );
		}
        
        $this->_save($data);
    }
    
	public function getActive($date) {
        $collection = array();
        $count      = 0;
        $sum        = 0;
        
        $from       = new \DateTime(sprintf('%s-%s-01 00:00:00', $date->format('Y'), $date->format('m')));
        
        /* Convert to store datetime */
        $from->add(new \DateInterval("PT7H"));
        
        $to = clone $date;
        
        /* Convert to store datetime */
        $to->add(new \DateInterval("PT7H"));
        
        $data = $this->resource->getConnection(\Magento\Framework\App\ResourceConnection::DEFAULT_CONNECTION)->fetchRow(
			sprintf(
				'
                    SELECT
                        COUNT(profile_id) AS count,
                        SUM(grand_total) AS sum
                    FROM %s AS main_table
                    WHERE
                        created_at <= "%s"
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
    
	public function getSuspended($date) {
        $collection = array();
        $count      = 0;
        $sum        = 0;
        
        $from       = new \DateTime(sprintf('%s-%s-01 00:00:00', $date->format('Y'), $date->format('m')));
        
        /* Convert to store datetime */
        $from->add(new \DateInterval("PT7H"));
        
        $to = clone $date;
        
        /* Convert to store datetime */
        $to->add(new \DateInterval("PT7H"));
        
        $data = $this->resource->getConnection(\Magento\Framework\App\ResourceConnection::DEFAULT_CONNECTION)->fetchRow(
			sprintf(
				'
                    SELECT
                        COUNT(profile_id) AS count,
                        SUM(grand_total) AS sum
                    FROM %s AS main_table
                    WHERE
                        created_at <= "%s"
                        AND status = "%s"
                        AND suspended_at IS NOT NULL
                        AND suspended_at < "%s"
                ',
				$this->resource->getTableName('subscriptions_profiles'),
                
                $to->format('Y-m-d H:i:s'),
                
                \Toppik\Subscriptions\Model\Profile::STATUS_SUSPENDED,
                $to->format('Y-m-d H:i:s')
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
    
	public function getCancelled($date) {
        $collection = array();
        $count      = 0;
        $sum        = 0;
        
        $from       = new \DateTime(sprintf('%s-%s-01 00:00:00', $date->format('Y'), $date->format('m')));
        
        /* Convert to store datetime */
        $from->add(new \DateInterval("PT7H"));
        
        $to = clone $date;
        
        /* Convert to store datetime */
        $to->add(new \DateInterval("PT7H"));
        
        $data = $this->resource->getConnection(\Magento\Framework\App\ResourceConnection::DEFAULT_CONNECTION)->fetchRow(
			sprintf(
				'
                    SELECT
                        COUNT(profile_id) AS count,
                        SUM(grand_total) AS sum
                    FROM %s AS main_table
                    WHERE
                        created_at <= "%s"
                        AND status = "%s"
                        AND cancelled_at IS NOT NULL
                        AND cancelled_at < "%s"
                ',
				$this->resource->getTableName('subscriptions_profiles'),
                
                $to->format('Y-m-d H:i:s'),
                \Toppik\Subscriptions\Model\Profile::STATUS_CANCELLED,
                $to->format('Y-m-d H:i:s')
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
    
	public function getNewSubscriptions($date) {
        $collection = array();
        $count      = 0;
        $sum        = 0;
        
        $from       = new \DateTime(sprintf('%s-%s-01 00:00:00', $date->format('Y'), $date->format('m')));
        
        /* Convert to store datetime */
        $from->add(new \DateInterval("PT7H"));
        
        $to = clone $date;
        
        /* Convert to store datetime */
        $to->add(new \DateInterval("PT7H"));
        
        $data = $this->resource->getConnection(\Magento\Framework\App\ResourceConnection::DEFAULT_CONNECTION)->fetchRow(
			sprintf(
				'
                    SELECT
                        COUNT(profile_id) AS count,
                        SUM(grand_total) AS sum
                    FROM %s AS main_table
                    WHERE
                        created_at BETWEEN "%s" AND "%s"
                ',
				$this->resource->getTableName('subscriptions_profiles'),
                
                $from->format('Y-m-d H:i:s'),
                $to->format('Y-m-d H:i:s')
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
    
	public function getSuspendedMonth($date) {
        $collection = array();
        $count      = 0;
        $sum        = 0;
        
        $from       = new \DateTime(sprintf('%s-%s-01 00:00:00', $date->format('Y'), $date->format('m')));
        
        /* Convert to store datetime */
        $from->add(new \DateInterval("PT7H"));
        
        $to = clone $date;
        
        /* Convert to store datetime */
        $to->add(new \DateInterval("PT7H"));
        
        $data = $this->resource->getConnection(\Magento\Framework\App\ResourceConnection::DEFAULT_CONNECTION)->fetchRow(
			sprintf(
				'
                    SELECT
                        COUNT(profile_id) AS count,
                        SUM(grand_total) AS sum
                    FROM %s AS main_table
                    WHERE
                        status = "%s"
                        AND suspended_at IS NOT NULL
                        AND suspended_at BETWEEN "%s" AND "%s"
                ',
				$this->resource->getTableName('subscriptions_profiles'),
                
                \Toppik\Subscriptions\Model\Profile::STATUS_SUSPENDED,
                $from->format('Y-m-d H:i:s'),
                $to->format('Y-m-d H:i:s')
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
    
	public function getCancelledMonth($date) {
        $collection = array();
        $count      = 0;
        $sum        = 0;
        
        $from       = new \DateTime(sprintf('%s-%s-01 00:00:00', $date->format('Y'), $date->format('m')));
        
        /* Convert to store datetime */
        $from->add(new \DateInterval("PT7H"));
        
        $to = clone $date;
        
        /* Convert to store datetime */
        $to->add(new \DateInterval("PT7H"));
        
        $data = $this->resource->getConnection(\Magento\Framework\App\ResourceConnection::DEFAULT_CONNECTION)->fetchRow(
			sprintf(
				'
                    SELECT
                        COUNT(profile_id) AS count,
                        SUM(grand_total) AS sum
                    FROM %s AS main_table
                    WHERE
                        status = "%s"
                        AND cancelled_at IS NOT NULL
                        AND cancelled_at BETWEEN "%s" AND "%s"
                ',
				$this->resource->getTableName('subscriptions_profiles'),
                
                \Toppik\Subscriptions\Model\Profile::STATUS_CANCELLED,
                $from->format('Y-m-d H:i:s'),
                $to->format('Y-m-d H:i:s')
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
                $this->resource->getTableName('subscription_report_pivot')
            )
        );
        
        if(count($data)) {
            $values = array();
            
            foreach($data as $_data) {
                $values[] = vsprintf('("%s", "%s", "%s", "%s", "%s", "%s", "%s", "%s", "%s", "%s", "%s", "%s", "%s")', $_data);
            }
            
            $this->resource->getConnection(\Magento\Framework\App\ResourceConnection::DEFAULT_CONNECTION)->query(
                sprintf(
                    'INSERT INTO %s (
                        period,
                        count_active,
                        sum_active,
                        count_suspended,
                        sum_suspended,
                        count_cancelled,
                        sum_cancelled,
                        count_new_month,
                        sum_new_month,
                        count_suspended_month,
                        sum_suspended_month,
                        count_cancelled_month,
                        sum_cancelled_month
                    ) VALUES %s',
                    $this->resource->getTableName('subscription_report_pivot'),
                    implode(',', $values)
                )
            );
        }
    }
    
}
