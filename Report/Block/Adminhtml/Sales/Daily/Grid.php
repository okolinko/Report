<?php
namespace Toppik\Report\Block\Adminhtml\Sales\Daily;

class Grid extends \Magento\Reports\Block\Adminhtml\Grid\AbstractGrid
{
	
    /**
     * GROUP BY criteria
     *
     * @var string
     */
    protected $_columnGroupBy = 'period';
    
    /**
     * {@inheritdoc}
     * @codeCoverageIgnore
     */
    protected function _construct() {
        $this->setSaveParametersInSession(false);
        $this->setCountTotals(true);
        parent::_construct();
    }
	
    /**
     * {@inheritdoc}
     */
    public function getResourceCollectionName() {
        return 'Toppik\Report\Model\ResourceModel\Report\Sales\Daily\Collection';
    }
	
    /**
     * {@inheritdoc}
     *
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     */
    protected function _prepareColumns() {
        $this->addColumn(
            'date_range',
            [
                'header' => __('Ship Date'),
                'index' => 'date_range',
                'type' => 'text',
                'totals_label' => 'Totals',
                'sortable' => true,
                'header_css_class' => 'col-orders',
                'column_css_class' => 'col-orders'
            ]
        );

        $this->addColumn(
            'source',
            [
                'header' => __('Order Source'),
                'index' => 'source',
                'type' => 'text',
                'totals_label' => '',
                'sortable' => true,
                'header_css_class' => 'col-orders',
                'column_css_class' => 'col-orders'
            ]
        );

        $this->addColumn(
            'merchant_source',
            [
                'header' => __('Merchant Source'),
                'index' => 'merchant_source',
                'type' => 'text',
                'sortable' => true,
                'header_css_class' => 'col-orders',
                'column_css_class' => 'col-orders'
            ]
        );
        
        $this->addColumn(
            'project_code',
            [
                'header' => __('Project_Code'),
                'index' => 'project_code',
                'type' => 'text',
                'sortable' => false,
                'header_css_class' => 'col-orders',
                'column_css_class' => 'col-orders'
            ]
        );
        
        $this->addColumn(
            'media_code',
            [
                'header' => __('Media_Code'),
                'index' => 'media_code',
                'type' => 'text',
                'sortable' => false,
                'header_css_class' => 'col-orders',
                'column_css_class' => 'col-orders'
            ]
        );
        
        $this->addColumn(
            'campaign_description',
            [
                'header' => __('Campaign_Description'),
                'index' => 'campaign_description',
                'type' => 'text',
                'sortable' => false,
                'header_css_class' => 'col-orders',
                'column_css_class' => 'col-orders'
            ]
        );
        
        $this->addColumn(
            'grand_total',
            [
                'header' => __('Grand Total'),
                'index' => 'grand_total',
                'type' => 'number',
                'total' => 'sum',
                'sortable' => false,
                'header_css_class' => 'col-orders',
                'column_css_class' => 'col-orders'
            ]
        );

        $this->addColumn(
            'subtotal',
            [
                'header' => __('Subotal'),
                'index' => 'subtotal',
                'type' => 'number',
                'total' => 'sum',
                'sortable' => false,
                'header_css_class' => 'col-orders',
                'column_css_class' => 'col-orders'
            ]
        );

        $this->addColumn(
            'shipping',
            [
                'header' => __('Shipping'),
                'index' => 'shipping',
                'type' => 'number',
                'total' => 'sum',
                'sortable' => false,
                'header_css_class' => 'col-orders',
                'column_css_class' => 'col-orders'
            ]
        );

        $this->addColumn(
            'taxes',
            [
                'header' => __('Taxes'),
                'index' => 'taxes',
                'type' => 'number',
                'total' => 'sum',
                'sortable' => false,
                'header_css_class' => 'col-orders',
                'column_css_class' => 'col-orders'
            ]
        );

        $this->addColumn(
            'discount',
            [
                'header' => __('Discount'),
                'index' => 'discount',
                'type' => 'number',
                'total' => 'sum',
                'sortable' => false,
                'header_css_class' => 'col-orders',
                'column_css_class' => 'col-orders'
            ]
        );

        $this->addColumn(
            'refund',
            [
                'header' => __('Refund'),
                'index' => 'refund',
                'type' => 'number',
                'total' => 'sum',
                'sortable' => false,
                'header_css_class' => 'col-orders',
                'column_css_class' => 'col-orders'
            ]
        );

        $this->addColumn(
            'order_volume',
            [
                'header' => __('Order Volume'),
                'index' => 'order_volume',
                'type' => 'number',
                'total' => 'sum',
                'sortable' => false,
                'header_css_class' => 'col-orders',
                'column_css_class' => 'col-orders'
            ]
        );

        $this->addColumn(
            'total_unit_sales',
            [
                'header' => __('Total Unit Sales'),
                'index' => 'total_unit_sales',
                'type' => 'number',
                'total' => 'sum',
                'sortable' => false,
                'header_css_class' => 'col-orders',
                'column_css_class' => 'col-orders'
            ]
        );



        $this->addColumn(
            'created_at_min',
            [
                'header' => __('Created at min'),
                'index' => 'created_at_min',
                'type' => 'text',
                'sortable' => false,
                'header_css_class' => 'col-orders',
                'column_css_class' => 'col-orders'
            ]
        );       


        $this->addColumn(
            'created_at_max',
            [
                'header' => ('Created at max'),
                'index' => 'created_at_max',
                'type' => 'text',
                'sortable' => false,
                'header_css_class' => 'col-orders',
                'column_css_class' => 'col-orders'
            ]
        );


 
        $this->addExportType('*/*/ExportDailyCsv', __('CSV'));
		
        return parent::_prepareColumns();
    }

    /**
     * @return array
     */
    public function getCountTotals()
    {
        if (!$this->getTotals()) {
            $filterData = $this->getFilterData();
            $totalsCollection = $this->_resourceFactory->create(
                $this->getResourceCollectionName()
            )->setDateRange(
                $filterData->getData('from', null),
                $filterData->getData('to', null)
            )->setCustomerGroups(
                $filterData->getData('customer_group')
            )->setGrouping(
                $filterData->getData('grouping')
            )->setAggregatedColumns(
                $this->_getAggregatedColumns()
            )->isTotals(
                true
            )->makeTotalSelect();

            if ($totalsCollection->load()->getSize() < 1 || !$filterData->getData('from')) {
                $this->setTotals(new \Magento\Framework\DataObject());
                $this->setCountTotals(false);
            } else {
                foreach ($totalsCollection->getItems() as $item) {
                    $this->setTotals($item);
                    break;
                }
            }
        }
        return $this->_countTotals;
    }

    /**
     * Set totals
     *
     * @param \Magento\Framework\DataObject $totals
     * @return void
     */
    public function setTotals(\Magento\Framework\DataObject $totals)
    {
        if(empty($this->_varTotals)){
            $this->_varTotals = $totals;
        }
    }

    /**
     * Add order status filter
     *
     * @param \Magento\Reports\Model\ResourceModel\Report\Collection\AbstractCollection $collection
     * @param \Magento\Framework\DataObject $filterData
     * @return $this
     */
    protected function _addOrderStatusFilter($collection, $filterData) {
        return $this;
    }



    /**
     * Adds custom filter to resource collection
     * Can be overridden in child classes if custom filter needed
     *
     * @param \Magento\Reports\Model\ResourceModel\Report\Collection\AbstractCollection $collection
     * @param \Magento\Framework\DataObject $filterData
     * @return $this
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     * @codeCoverageIgnore
     */
    protected function _addCustomFilter($collection, $filterData) {


        $collection->setCustomerGroups($filterData->getData('customer_group'));
        $collection->setGrouping($filterData->getData('grouping'));


        return $this;
    }


}
