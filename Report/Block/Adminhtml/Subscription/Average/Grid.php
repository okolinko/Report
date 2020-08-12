<?php
namespace Toppik\Report\Block\Adminhtml\Subscription\Average;

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
        return 'Toppik\Report\Model\ResourceModel\Report\Subscription\Average\Collection';
    }
	
    /**
     * {@inheritdoc}
     *
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     */
    protected function _prepareColumns() {
        $this->addColumn(
            'subscription_period',
            [
                'header' => __('Frequency (Days)'),
                'index' => 'subscription_period',
                'type' => 'number',
                'sortable' => false,
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
                'sortable' => false,
                'header_css_class' => 'col-orders',
                'column_css_class' => 'col-orders'
            ]
        );
		
        $this->addColumn(
            'count_active',
            [
                'header' => __('# Of Active Subscriptions'),
                'index' => 'count_active',
                'type' => 'number',
                'sortable' => false,
                'renderer' => 'Toppik\Report\Block\Adminhtml\Widget\Grid\Column\Renderer\Subscription\Average\Active',
                'header_css_class' => 'col-orders',
                'column_css_class' => 'col-orders'
            ]
        );
		
        $this->addColumn(
            'count_cencelled',
            [
                'header' => __('# Of Cancelled/Suspended Subscriptions'),
                'index' => 'count_cencelled',
                'type' => 'number',
                'sortable' => false,
                'renderer' => 'Toppik\Report\Block\Adminhtml\Widget\Grid\Column\Renderer\Subscription\Average\Cancelled',
                'header_css_class' => 'col-orders',
                'column_css_class' => 'col-orders'
            ]
        );
		
        $this->addColumn(
            'min_value_days',
            [
                'header' => __('Min (Days)'),
                'index' => 'min_value_days',
                'type' => 'number',
                'sortable' => false,
                'header_css_class' => 'col-orders',
                'column_css_class' => 'col-orders'
            ]
        );
		
        $this->addColumn(
            'max_value_days',
            [
                'header' => __('Max (Days)'),
                'index' => 'max_value_days',
                'type' => 'number',
                'sortable' => false,
                'header_css_class' => 'col-orders',
                'column_css_class' => 'col-orders'
            ]
        );
		
        $this->addColumn(
            'average_value_days',
            [
                'header' => __('Average (Days)'),
                'index' => 'average_value_days',
                'type' => 'number',
                'sortable' => false,
                'header_css_class' => 'col-orders',
                'column_css_class' => 'col-orders'
            ]
        );
		
        $this->addColumn(
            'count_orders',
            [
                'header' => __('# Of Orders'),
                'index' => 'count_orders',
                'type' => 'number',
                'sortable' => false,
                'header_css_class' => 'col-orders',
                'column_css_class' => 'col-orders'
            ]
        );
		
        $this->addExportType('*/*/ExportAverageCsv', __('CSV'));
		
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
