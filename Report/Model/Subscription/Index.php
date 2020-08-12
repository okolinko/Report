<?php
namespace Toppik\Report\Model\Subscription;

use Magento\Framework\App\ResourceConnection;

class Index extends \Magento\Framework\Model\AbstractModel {
	
	protected $_months = 25;
	
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
		$reportTable 	= $this->resource->getTableName('subscriptions_report');
        $connection 	= $this->resource->getConnection(ResourceConnection::DEFAULT_CONNECTION);
		
		$month	= date('m');
		$year 	= date('Y');
		
		$days 	= cal_days_in_month(CAL_GREGORIAN, $month, $year);
		$from 	= sprintf('%s-%s-01 00:00:00', $year, $month);
		
		$data = array();
		
		for($i = 0; $i < $this->_months; $i++) {
			$date = new \DateTime($from);
			$date->sub(new \DateInterval(sprintf('P%sM', $i)));
			$_from = $date->format('Y-m-d H:i:s');
			
			$days 	= cal_days_in_month(CAL_GREGORIAN, $date->format('m'), $date->format('Y'));
			
			$date = new \DateTime($_from);
			$date->add(new \DateInterval(sprintf('P%sD', $days)));
			$date->sub(new \DateInterval('PT1S'));
			$_to = $date->format('Y-m-d H:i:s');
			
			$_data = $connection->fetchAll(
				sprintf(
					'SELECT
					"%s" AS period_from,
					"%s" AS period_to,
					(
						SELECT entity_count
						FROM (
							SELECT COUNT(*) AS entity_count
							FROM sales_order
							WHERE created_at BETWEEN "%s" AND "%s"
						) AS oc
					) AS order_count,
					(
						SELECT entity_total
						FROM (
							SELECT "Order" AS entity_type, SUM(grand_total) AS entity_total
							FROM sales_order
							WHERE created_at BETWEEN "%s" AND "%s"
							GROUP BY entity_type
						) AS oc
					) AS order_total,
					(
						SELECT COUNT(*) AS entity_count
						FROM subscriptions_profiles
						WHERE created_at BETWEEN "%s" AND "%s"
					) AS subscription_count,
					(
						SELECT entity_total
						FROM (
							SELECT "Subscription" AS entity_type, SUM(grand_total) AS entity_total
							FROM subscriptions_profiles
							WHERE created_at BETWEEN "%s" AND "%s"
							GROUP BY entity_type
						) AS oc
					) AS subscription_total,
					(
						FORMAT(
							(
								(
									SELECT entity_count
									FROM (
										SELECT "Subscription" AS entity_type, COUNT(*) AS entity_count
										FROM sales_order
										WHERE created_at BETWEEN "%s" AND "%s" AND source IN("New AutoShip CS", "New AutoShip Web")
										GROUP BY entity_type
									) AS oc
								) * 100 / (
									SELECT entity_count
									FROM (
										SELECT "Order" AS entity_type, COUNT(*) AS entity_count
										FROM sales_order
										WHERE created_at BETWEEN "%s" AND "%s" AND source IN("Web", "Customer Service")
										GROUP BY entity_type
									) AS oc
								)
							),
							2
						)
					) AS percentage_subscription,
					(
						SELECT entity_count
						FROM (
							SELECT "Order" AS entity_type, COUNT(*) AS entity_count
							FROM sales_order
							WHERE created_at BETWEEN "%s" AND "%s" AND source = "Web"
							GROUP BY entity_type
						) AS oc
					) AS web_order_count,
					(
						SELECT entity_total
						FROM (
							SELECT "Order" AS entity_type, SUM(grand_total) AS entity_total
							FROM sales_order
							WHERE created_at BETWEEN "%s" AND "%s" AND source = "Web"
							GROUP BY entity_type
						) AS oc
					) AS web_order_total,
					(
						SELECT entity_count
						FROM (
							SELECT "Order" AS entity_type, COUNT(*) AS entity_count
							FROM sales_order
							WHERE created_at BETWEEN "%s" AND "%s" AND source = "Customer Service"
							GROUP BY entity_type
						) AS oc
					) AS cs_order_count,
					(
						SELECT entity_total
						FROM (
							SELECT "Order" AS entity_type, SUM(grand_total) AS entity_total
							FROM sales_order
							WHERE created_at BETWEEN "%s" AND "%s" AND source = "Customer Service"
							GROUP BY entity_type
						) AS oc
					) AS cs_order_total,
					(
						SELECT entity_count
						FROM (
							SELECT "Subscription" AS entity_type, COUNT(*) AS entity_count
							FROM sales_order
							WHERE created_at BETWEEN "%s" AND "%s" AND source = "New AutoShip Web"
							GROUP BY entity_type
						) AS sc
					) AS web_subscription_count,
					(
						SELECT entity_total
						FROM (
							SELECT "Subscription" AS entity_type, SUM(grand_total) AS entity_total
							FROM sales_order
							WHERE created_at BETWEEN "%s" AND "%s" AND source = "New AutoShip Web"
							GROUP BY entity_type
						) AS sc
					) AS web_subscription_total,
					(
						SELECT entity_count
						FROM (
							SELECT "Subscription" AS entity_type, COUNT(*) AS entity_count
							FROM sales_order
							WHERE created_at BETWEEN "%s" AND "%s" AND source = "New AutoShip CS"
							GROUP BY entity_type
						) AS sc
					) AS cs_subscription_count,
					(
						SELECT entity_total
						FROM (
							SELECT "Subscription" AS entity_type, SUM(grand_total) AS entity_total
							FROM sales_order
							WHERE created_at BETWEEN "%s" AND "%s" AND source = "New AutoShip CS"
							GROUP BY entity_type
						) AS sc
					) AS cs_subscription_total,
					(
						SELECT entity_count
						FROM (
							SELECT "Subscription" AS entity_type, COUNT(*) AS entity_count
							FROM sales_order
							WHERE created_at BETWEEN "%s" AND "%s" AND source = "autoship import"
							GROUP BY entity_type
						) AS sc
					) AS autoship_subscription_count,
					(
						SELECT entity_total
						FROM (
							SELECT "Subscription" AS entity_type, SUM(grand_total) AS entity_total
							FROM sales_order
							WHERE created_at BETWEEN "%s" AND "%s" AND source = "autoship import"
							GROUP BY entity_type
						) AS sc
					) AS autoship_subscription_total',
					$_from,
					$_to,
					$_from,
					$_to,
					$_from,
					$_to,
					$_from,
					$_to,
					$_from,
					$_to,
					$_from,
					$_to,
					$_from,
					$_to,
					$_from,
					$_to,
					$_from,
					$_to,
					$_from,
					$_to,
					$_from,
					$_to,
					$_from,
					$_to,
					$_from,
					$_to,
					$_from,
					$_to,
					$_from,
					$_to,
					$_from,
					$_to,
					$_from,
					$_to
				)
			);
			
			$data = array_merge($data, $_data);
		}
		
		if(count($data)) {
			$values = array();
			
			foreach($data as $_data) {
				$values[] = sprintf(
					'("%s", "%s", "%s", "%s", "%s", "%s", "%s", "%s", "%s", "%s", "%s", "%s", "%s", "%s", "%s", "%s", "%s", "%s", "%s")',
					isset($_data['period_from']) ? $_data['period_from'] : 0,
					isset($_data['period_to']) ? $_data['period_to'] : 0,
					isset($_data['order_count']) ? $_data['order_count'] : 0,
					isset($_data['order_total']) ? $_data['order_total'] : 0,
					isset($_data['subscription_count']) ? $_data['subscription_count'] : 0,
					isset($_data['subscription_total']) ? $_data['subscription_total'] : 0,
					isset($_data['percentage_subscription']) ? $_data['percentage_subscription'] : 0,
					isset($_data['web_order_count']) ? $_data['web_order_count'] : 0,
					isset($_data['web_order_total']) ? $_data['web_order_total'] : 0,
					isset($_data['cs_order_count']) ? $_data['cs_order_count'] : 0,
					isset($_data['cs_order_total']) ? $_data['cs_order_total'] : 0,
					isset($_data['web_subscription_count']) ? $_data['web_subscription_count'] : 0,
					isset($_data['web_subscription_total']) ? $_data['web_subscription_total'] : 0,
					isset($_data['cs_subscription_count']) ? $_data['cs_subscription_count'] : 0,
					isset($_data['cs_subscription_total']) ? $_data['cs_subscription_total'] : 0,
					isset($_data['cancelled_subscription_count']) ? $_data['cancelled_subscription_count'] : 0,
					isset($_data['cancelled_subscription_total']) ? $_data['cancelled_subscription_total'] : 0,
					isset($_data['autoship_subscription_count']) ? $_data['autoship_subscription_count'] : 0,
					isset($_data['autoship_subscription_total']) ? $_data['autoship_subscription_total'] : 0
				);
			}
			
			$connection->query(sprintf('TRUNCATE %s', $reportTable));
			
			$connection->query(
				sprintf(
					'INSERT INTO %s (
						period_from,
						period_to,
						order_count,
						order_total,
						subscription_count,
						subscription_total,
						percentage_subscription,
						web_order_count,
						web_order_total,
						cs_order_count,
						cs_order_total,
						web_subscription_count,
						web_subscription_total,
						cs_subscription_count,
						cs_subscription_total,
						cancelled_subscription_count,
						cancelled_subscription_total,
						autoship_subscription_count,
						autoship_subscription_total
					) VALUES %s',
					$reportTable,
					implode(',', $values)
				)
			);
		}
    }
	
}
