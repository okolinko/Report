<?php
namespace Toppik\Report\Controller\Debug;

use Magento\Framework\App\Action;
use Magento\Framework\App\Action\Context;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Controller\ResultInterface;

class CreateCoupon extends Action\Action {
	
    /**
     * @var ResourceConnection
     */
    protected $_resource;
	
    /**
     * @var \Magento\Framework\App\Response\Http\FileFactory
     */
    protected $_fileFactory;
	
	protected $ruleFactory;
	protected $productRuleFactory;
	protected $foundProductRuleFactory;
	protected $ruleResource;
	
	public function __construct(
        Action\Context $context,
		\Magento\SalesRule\Model\RuleFactory $ruleFactory,
		\Magento\SalesRule\Model\Rule\Condition\ProductFactory $productRuleFactory,
		\Magento\SalesRule\Model\Rule\Condition\Product\FoundFactory $foundProductRuleFactory,
		\Magento\SalesRule\Model\ResourceModel\Rule $ruleResource,
        \Magento\Framework\App\Response\Http\FileFactory $fileFactory
	) {
		$this->ruleFactory = $ruleFactory;
		$this->productRuleFactory = $productRuleFactory;
		$this->foundProductRuleFactory = $foundProductRuleFactory;
		$this->ruleResource = $ruleResource;
        $this->_fileFactory = $fileFactory;
        parent::__construct($context);
	}
	
    public function execute() {
		$total 		= 15840;
		$discount 	= 5;
		$format 	= '1M5%s';
		$errors 	= array();
		$collection = array();
		
		$i			= 1;
		
		while(count($collection) < $total) {
			try {
				$code = str_pad(sprintf($format, $i), 8, 0);
				
				$shoppingCartPriceRule = $this->ruleFactory->create();
				
				$shoppingCartPriceRule->setName(sprintf('$%s Discount', $discount))
										->setDescription(sprintf('$%s Discount', $discount))
										->setFromDate('2017-01-01')
										->setToDate('2017-12-31')
										->setTimesUsed('1')
										->setUsesPerCustomer('1')
										->setWebsiteIds(array('1'))
										->setCustomerGroupIds(array('0', '1', '2', '3', '4', '5', '6'))
										->setIsActive('1')
										->setStopRulesProcessing('0')
										->setIsAdvanced('1')
										->setProductIds(null)
										->setSortOrder('1')
										->setSimpleAction('cart_fixed')
										->setDiscountAmount($discount)
										->setDiscountQty(null)
										->setDiscountStep('0')
										->setSimpleFreeShipping('0')
										->setApplyToShipping('0')
										->setIsRss('0')
										->setCouponType('2')
										->setCouponCode($code)
										->setUsesPerCoupon(1);
				
				$this->ruleResource->save($shoppingCartPriceRule);
				
				$collection[] = array('code' => $code);
			} catch(\Exception $e) {
				$errors[] = $e->getMessage();
			}
			
			$i = $i + 3;
		}
		
        $headers 	= array('code');
		$rows 		= $this->_generateRows($collection, $headers);
		$csv 		= $this->_generateFile($rows, $headers);
		
        return $this->_fileFactory->create('coupons.csv', $csv, \Magento\Framework\App\Filesystem\DirectoryList::VAR_DIR);
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

/*
	SELECT e.rule_id FROM salesrule AS e
	INNER JOIN salesrule_coupon AS c ON c.rule_id = e.rule_id
	WHERE c.code LIKE '%2017MARCH5D0%'
	
	
	UPDATE salesrule SET simple_action = 'cart_fixed' WHERE rule_id IN (
		SELECT id FROM (
			SELECT e.rule_id AS id FROM salesrule AS e
			INNER JOIN salesrule_coupon AS c ON c.rule_id = e.rule_id
			WHERE c.code LIKE '%2017MARCH5D0%'
		) AS m
	)
	
	
	DELETE FROM salesrule_website WHERE rule_id IN (
		SELECT id FROM (
			SELECT e.rule_id AS id FROM salesrule AS e
			INNER JOIN salesrule_coupon AS c ON c.rule_id = e.rule_id
			WHERE c.code LIKE '%2017MARCH%'
		) AS m
	)

	DELETE FROM salesrule WHERE rule_id IN (
		SELECT id FROM (
			SELECT e.rule_id AS id FROM salesrule AS e
			INNER JOIN salesrule_coupon AS c ON c.rule_id = e.rule_id
			WHERE c.code LIKE '%2017MARCH%'
		) AS m
	)

	DELETE FROM salesrule_coupon WHERE code LIKE '%2017MARCH%'
*/
