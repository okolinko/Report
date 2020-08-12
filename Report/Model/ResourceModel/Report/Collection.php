<?php

namespace Toppik\Report\Model\ResourceModel\Report;

use Magento\Framework\Stdlib\DateTime\DateTime;

class Collection extends \Magento\Sales\Model\ResourceModel\Report\Collection\AbstractCollection
{
    /**
     * Period format
     *
     * @var string
     */
    protected $_periodFormat;

    /**
     * Aggregated Data Table
     *
     * @var string
     */
    protected $_mainTable = 'sales_order_item';

    /**
     * Selected columns
     *
     * @var array
     */
    protected $_selectedColumns = [];
    /**
     * @var \Magento\Framework\Stdlib\DateTime\TimezoneInterface
     */
    protected $tz;

    /**
     * @param \Magento\Framework\Stdlib\DateTime\TimezoneInterface $tz
     * @param \Magento\Framework\Data\Collection\EntityFactory $entityFactory
     * @param \Psr\Log\LoggerInterface $logger
     * @param \Magento\Framework\Data\Collection\Db\FetchStrategyInterface $fetchStrategy
     * @param \Magento\Framework\Event\ManagerInterface $eventManager
     * @param \Magento\Sales\Model\ResourceModel\Report $resource
     * @param \Magento\Framework\DB\Adapter\AdapterInterface $connection
     */
    public function __construct(
        \Magento\Framework\Stdlib\DateTime\TimezoneInterface $tz,
        \Magento\Framework\Data\Collection\EntityFactory $entityFactory,
        \Psr\Log\LoggerInterface $logger,
        \Magento\Framework\Data\Collection\Db\FetchStrategyInterface $fetchStrategy,
        \Magento\Framework\Event\ManagerInterface $eventManager,
        \Magento\Sales\Model\ResourceModel\Report $resource,
        \Magento\Framework\DB\Adapter\AdapterInterface $connection = null
    ) {
        $resource->init($this->_mainTable);
        $this->tz = $tz;
        parent::__construct($entityFactory, $logger, $fetchStrategy, $eventManager, $resource, $connection);
    }

    protected function initSelect()
    {
        $this->_reset();
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();

        $helper = $objectManager->get('Magento\Backend\Helper\Data');
        $request = $objectManager->get('\Magento\Framework\App\Request\Http');
        $filterParams = $helper->prepareFilterString(
            $request->getParam('filter')
        );
        
        $this->getSelect()
            ->from(['main_table' => $this->_mainTable])
            ->joinInner(array(
                'o' =>  $this->getTable('sales_order')),
                'o.entity_id = main_table.order_id'
            )
            ->joinInner(array(
                'oa'    =>  $this->getTable('sales_order_address')),
                'oa.parent_id = main_table.order_id AND oa.address_type = "shipping"'
            )
            ->joinInner(array(
                'billing'    =>  $this->getTable('sales_order_address')),
                'billing.parent_id = main_table.order_id AND billing.address_type = "billing"'
            )
            ->joinLeft(array(
                's'    =>  $this->getTable('sales_shipment')),
                's.order_id = main_table.order_id'
            )
            ->joinLeft(array(
                'i'    =>  $this->getTable('sales_invoice')),
                'i.order_id = main_table.order_id'
            )
            ->joinLeft(array(
                'op'    =>  $this->getTable('sales_order_payment')),
                'op.parent_id = main_table.order_id'
            )
            ->joinLeft(array(
                'cg'    =>  $this->getTable('customer_group')),
                'o.customer_group_id = cg.customer_group_id'
            )
            ->joinLeft(array(
                'member_type'    =>  'customer_entity_int'),
                'member_type.entity_id = o.customer_id AND member_type.attribute_id=209'
            )
            ->joinLeft(array(
                'eaov1'    =>  'eav_attribute_option_value'),
                'member_type.value = eaov1.option_id'
            )
            ->joinLeft(array(
                'customer_class'    =>  'customer_entity_int'),
                'customer_class.entity_id = o.customer_id AND customer_class.attribute_id=210'
            )
            ->joinLeft(array(
                'eaov2'    =>  'eav_attribute_option_value'),
                'customer_class.value = eaov2.option_id'
            )
            ->joinLeft(array(
                'st'    =>  $this->getTable('sales_shipment_track')),
                'st.order_id = s.order_id'
            )
            ->joinLeft(array(
                'mod'    =>  $this->getTable('microsite_order_detail')),
                'mod.order_id = o.entity_id',
                array()
            )
            ->joinLeft(array(
                'ce'    =>  'customer_entity'),
                'o.customer_id = ce.entity_id'
            )
            ->where('main_table.parent_item_id is null')
            ->where('oa.address_type = "shipping"')
            ->columns(array(
                'main_table.*',
                'ROUND(((main_table.row_total + (main_table.base_discount_amount * -1)) / main_table.qty_ordered), 2) AS discounted_price',
                'ROUND(main_table.row_total + (main_table.base_discount_amount * -1), 2) AS discounted_row_total',
                'o.increment_id',
                'o.customer_id',
                'ce.email AS customer_email',
                'ce.created_at as customer_since_from',
                'o.rep_code AS rep_code',
                'oa.company',
                'cg.customer_group_code AS customer_group_label',
                'o.customer_firstname',
                "SUBSTRING_INDEX(SUBSTRING_INDEX(CONCAT(oa.street, REPEAT('\\n', 3)), '\\n', 1), '\\n', -1) as street_1",
                "SUBSTRING_INDEX(SUBSTRING_INDEX(CONCAT(oa.street, REPEAT('\\n', 3)), '\\n', 2), '\\n', -1) as street_2",
                "SUBSTRING_INDEX(SUBSTRING_INDEX(CONCAT(oa.street, REPEAT('\\n', 3)), '\\n', 3), '\\n', -1) as street_3",
                'oa.city',
                'oa.region',
                'oa.postcode',
                'oa.country_id',
                'oa.telephone',
                "SUBSTRING_INDEX(SUBSTRING_INDEX(CONCAT(billing.street, REPEAT('\\n', 3)), '\\n', 1), '\\n', -1) AS billing_street_1",
                "SUBSTRING_INDEX(SUBSTRING_INDEX(CONCAT(billing.street, REPEAT('\\n', 3)), '\\n', 2), '\\n', -1) AS billing_street_2",
                "SUBSTRING_INDEX(SUBSTRING_INDEX(CONCAT(billing.street, REPEAT('\\n', 3)), '\\n', 3), '\\n', -1) AS billing_street_3",
                'billing.city AS billing_city',
                'billing.region AS billing_region',
                'billing.postcode AS billing_postcode',
                'billing.country_id AS billing_country_id',
                'billing.telephone AS billing_telephone',
                "CONCAT('','') as customer_po",
                's.created_at as shipped_at',
                'main_table.qty_ordered',
                "REPLACE(REPLACE(REPLACE(main_table.name, '<br>', ' '), '<br/>', ' '), '<br />', ' ') AS `name`",
                'ROUND(o.grand_total, 2) as order_total',
                "CONCAT('E','A') as uom_id",
                'op.cc_type',
                'op.po_number',
                'o.discount_description',
                'o.shipping_amount',
                'IFNULL(eaov1.value, "Consumer") as member_type_value',
                'IFNULL(eaov2.value, "Consumer") as customer_class_value',
                'st.track_number',
                'o.shipping_method',
                'i.created_at as payment_date',
                'o.shipping_tax_amount',
                'o.ship_by',
                'o.source',
				'o.merchant_source',
                'IF(op.method="braintree", "Credit Card",IF(op.method="purchaseorder", "Purchase Order", IF(op.method="free", "Free",op.method))) as payment_type',
                'main_table.tax_amount as tax_amount',
                'o.status AS order_status',
                'mod.project_code AS project_code',
                'mod.media_code AS media_code',
                'mod.campaign_description AS campaign_description'
            ))
            ->group('main_table.item_id') // to prevent "Item with the same id already exist error"
            ->order('s.created_at ASC');
        
        if(isset($filterParams['status']) && (bool) $filterParams['status'] !== false) {
            $this->getSelect()->where('o.status = ?', $filterParams['status']);
        }
        
        if(isset($filterParams['customer_group_ids']) && (bool) $filterParams['customer_group_ids'] !== false) {
            $customer_group_ids = $filterParams['customer_group_ids'];
            
            if(isset($customer_group_ids[0])) {
                $customer_group_ids = explode(',', $customer_group_ids[0]);
                $this->getSelect()->where('o.customer_group_id IN (?)', $customer_group_ids);
            }
        }
        
        if(isset($filterParams['product_sku']) && !empty($filterParams['product_sku'])) {
            $product_sku = (string) $filterParams['product_sku'];
            
            if($product_sku && !empty($product_sku)) {
                $items = array();
                $product_sku = explode(',', $product_sku);
                
                if(count($product_sku)) {
                    foreach($product_sku as $_item) {
                        $_item = trim($_item);
                        
                        if(!empty($_item)) {
                            $items[] = $_item;
                        }
                    }
                }
                
                if(count($items)) {
                    $this->getSelect()->where('main_table.sku IN (?)', $items);
                }
            }
        }
        
        return parent::_beforeLoad();
    }

    public function setDateRange($from = NULL, $to = NULL) {
        $this->initSelect();
        if ($this->isOrderIdRange($from, $to)) {
            $this->getSelect()
                ->where("o.increment_id BETWEEN ".$from." AND ".$to)
                ->where("s.created_at<>''");
        } else {
            $date = new \DateTime($from, new \DateTimeZone($this->tz->getConfigTimezone()));
            $date->setTimezone(new \DateTimeZone($this->tz->getDefaultTimezone()));
            $date->sub(new \DateInterval('PT8H'));
            $from = $date->format('Y-m-d H:i:s');
            $date = new \DateTime($to, new \DateTimeZone($this->tz->getConfigTimezone()));
            $date->setTimezone(new \DateTimeZone($this->tz->getDefaultTimezone()));
            $date->sub(new \DateInterval('PT8H'));
            $to = $date->format('Y-m-d H:i:s');
            $this->getSelect()
                ->where("`s`.`created_at` BETWEEN '".$from."' AND '".$to."'");
        }
        // uncomment next line to get the query log:
        // Mage::log('SQL: '.$this->getSelect()->__toString());
        return $this;
    }

    /**
     * Apply stores filter to select object
     *
     * @param \Magento\Framework\DB\Select $select
     * @return $this
     */
    protected function _applyStoresFilterToSelect(\Magento\Framework\DB\Select $select)
    {
        return $this;
    }

    public function isOrderIdRange($from, $to)
    {
        return (
            strlen($from) == 9
            && strlen($to) == 9
            && is_numeric($from)
            && is_numeric($to)
        );
    }
}
