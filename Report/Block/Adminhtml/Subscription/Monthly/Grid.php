<?php
namespace Toppik\Report\Block\Adminhtml\Subscription\Monthly;

class Grid extends \Magento\Reports\Block\Adminhtml\Grid\AbstractGrid {
	
    /**
     * {@inheritdoc}
     * @codeCoverageIgnore
     */
    protected function _construct() {
        $this->setSaveParametersInSession(false);
        parent::_construct();
    }
	
    /**
     * {@inheritdoc}
     */
    public function getResourceCollectionName() {
        return 'Toppik\Report\Model\ResourceModel\Report\Subscription\Monthly\Collection';
    }
	
    /**
     * {@inheritdoc}
     *
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     */
    protected function _prepareColumns() {
        $this->addColumn(
            'period_from',
            [
                'header' => __('Period From'),
                'index' => 'period_from',
                'type' => 'datetime',
                'renderer' => 'Magento\Reports\Block\Adminhtml\Sales\Grid\Column\Renderer\Date',
				'format' => 'MMM d, y HH:mm:ss',
                'sortable' => false,
                'header_css_class' => 'col-orders',
                'column_css_class' => 'col-orders'
            ]
        );
		
        $this->addColumn(
            'period_to',
            [
                'header' => __('Period To'),
                'index' => 'period_to',
                'type' => 'datetime',
                'renderer' => 'Magento\Reports\Block\Adminhtml\Sales\Grid\Column\Renderer\Date',
				'format' => 'MMM d, y HH:mm:ss',
                'sortable' => false,
                'header_css_class' => 'col-orders',
                'column_css_class' => 'col-orders'
            ]
        );
		
        $this->addColumn(
            'order_count',
            [
                'header' => __('# Of Created Orders'),
                'index' => 'order_count',
                'type' => 'number',
                'sortable' => false,
                'renderer' => 'Toppik\Report\Block\Adminhtml\Widget\Grid\Column\Renderer\Subscription\Monthly\CountAll',
                'header_css_class' => 'col-orders',
                'column_css_class' => 'col-orders'
            ]
        );
		
        $this->addColumn(
            'order_total',
            [
                'header' => __('Total Of Created Orders'),
                'index' => 'order_total',
                'type' => 'number',
                'sortable' => false,
                'renderer' => 'Toppik\Report\Block\Adminhtml\Widget\Grid\Column\Renderer\Subscription\Monthly\TotalAll',
                'header_css_class' => 'col-orders',
                'column_css_class' => 'col-orders'
            ]
        );
		
        $this->addColumn(
            'subscription_count',
            [
                'header' => __('# Of Created Subscriptions'),
                'index' => 'subscription_count',
                'type' => 'number',
                'sortable' => false,
                'header_css_class' => 'col-orders',
                'column_css_class' => 'col-orders'
            ]
        );
		
        $this->addColumn(
            'subscription_total',
            [
                'header' => __('Total Of Created Subscriptions'),
                'index' => 'subscription_total',
                'type' => 'number',
                'sortable' => false,
                'header_css_class' => 'col-orders',
                'column_css_class' => 'col-orders'
            ]
        );
		
        $this->addColumn(
            'percentage_subscription',
            [
                'header' => __('Subscription Percentage'),
                'index' => 'percentage_subscription',
                'type' => 'number',
                'sortable' => false,
                'header_css_class' => 'col-orders',
                'column_css_class' => 'col-orders'
            ]
        );
		
        $this->addColumn(
            'web_subscription_count',
            [
                'header' => __('# Of Subscription Orders (Web)'),
                'index' => 'web_subscription_count',
                'type' => 'number',
                'sortable' => false,
                'renderer' => 'Toppik\Report\Block\Adminhtml\Widget\Grid\Column\Renderer\Subscription\Monthly\CountWeb',
                'header_css_class' => 'col-orders',
                'column_css_class' => 'col-orders'
            ]
        );
		
        $this->addColumn(
            'web_subscription_total',
            [
                'header' => __('Total Of Subscription Orders (Web)'),
                'index' => 'web_subscription_total',
                'type' => 'number',
                'sortable' => false,
                'renderer' => 'Toppik\Report\Block\Adminhtml\Widget\Grid\Column\Renderer\Subscription\Monthly\TotalWeb',
                'header_css_class' => 'col-orders',
                'column_css_class' => 'col-orders'
            ]
        );
		
        $this->addColumn(
            'cs_subscription_count',
            [
                'header' => __('# Of Subscription Orders (CS)'),
                'index' => 'cs_subscription_count',
                'type' => 'number',
                'sortable' => false,
                'renderer' => 'Toppik\Report\Block\Adminhtml\Widget\Grid\Column\Renderer\Subscription\Monthly\CountCs',
                'header_css_class' => 'col-orders',
                'column_css_class' => 'col-orders'
            ]
        );
		
        $this->addColumn(
            'cs_subscription_total',
            [
                'header' => __('Total Of Subscription Orders (CS)'),
                'index' => 'cs_subscription_total',
                'type' => 'number',
                'sortable' => false,
                'renderer' => 'Toppik\Report\Block\Adminhtml\Widget\Grid\Column\Renderer\Subscription\Monthly\TotalCs',
                'header_css_class' => 'col-orders',
                'column_css_class' => 'col-orders'
            ]
        );
		
        $this->addColumn(
            'autoship_subscription_count',
            [
                'header' => __('# Of Subscription Orders (Import)'),
                'index' => 'autoship_subscription_count',
                'type' => 'number',
                'sortable' => false,
                'renderer' => 'Toppik\Report\Block\Adminhtml\Widget\Grid\Column\Renderer\Subscription\Monthly\CountAutoship',
                'header_css_class' => 'col-orders',
                'column_css_class' => 'col-orders'
            ]
        );
		
        $this->addColumn(
            'autoship_subscription_total',
            [
                'header' => __('Total Of Subscription Orders (Import)'),
                'index' => 'autoship_subscription_total',
                'type' => 'number',
                'sortable' => false,
                'renderer' => 'Toppik\Report\Block\Adminhtml\Widget\Grid\Column\Renderer\Subscription\Monthly\TotalAutoship',
                'header_css_class' => 'col-orders',
                'column_css_class' => 'col-orders'
            ]
        );
		
        $this->addExportType('*/*/ExportMonthlyCsv', __('CSV'));
		
        return parent::_prepareColumns();
    }
	
    /**
     * @return array
     */
    public function getCountTotals() {
		return null;
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
        return $this;
    }
	
    /**
     * @return $this|\Magento\Backend\Block\Widget\Grid
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     * @SuppressWarnings(PHPMD.NPathComplexity)
     */
    protected function _prepareCollection() {
		$this->setCountTotals(false);
		$this->setCountSubTotals(false);
		
        $resourceCollection = $this->_resourceFactory->create(
            $this->getResourceCollectionName()
        )
		->initSelect();
		
		$this->setCollection($resourceCollection);
		
        if($this->_isExport) {
            return $this;
        }
		
		$this->getCollection()->load();
		$this->_afterLoadCollection();
		
		return $this;
    }
	
}
