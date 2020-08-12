<?php
namespace Toppik\Report\Block\Adminhtml\Subscription\Pivot;

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
        return 'Toppik\Report\Model\ResourceModel\Report\Subscription\Pivot\Collection';
    }
	
    /**
     * {@inheritdoc}
     *
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     */
    protected function _prepareColumns() {
        $this->addColumn(
            'period',
            [
                'header' => __('Period'),
                'index' => 'period',
                'type' => 'text',
                'sortable' => false,
                'header_css_class' => 'col-orders',
                'column_css_class' => 'col-orders'
            ]
        );
        
        $this->addColumn(
            'count_active',
            [
                'header' => __('Active'),
                'index' => 'count_active',
                'type' => 'number',
                'sortable' => false,
                'renderer' => 'Toppik\Report\Block\Adminhtml\Widget\Grid\Column\Renderer\Subscription\Monthly\YearRow',
                'header_css_class' => 'col-orders',
                'column_css_class' => 'col-orders'
            ]
        );
        
        $this->addColumn(
            'count_cancelled',
            [
                'header' => __('Cancelled'),
                'index' => 'count_cancelled',
                'type' => 'number',
                'sortable' => false,
                'renderer' => 'Toppik\Report\Block\Adminhtml\Widget\Grid\Column\Renderer\Subscription\Monthly\YearRow',
                'header_css_class' => 'col-orders',
                'column_css_class' => 'col-orders'
            ]
        );
        
        $this->addColumn(
            'count_suspended',
            [
                'header' => __('Suspended'),
                'index' => 'count_suspended',
                'type' => 'number',
                'sortable' => false,
                'renderer' => 'Toppik\Report\Block\Adminhtml\Widget\Grid\Column\Renderer\Subscription\Monthly\YearRow',
                'header_css_class' => 'col-orders',
                'column_css_class' => 'col-orders'
            ]
        );
        
        $this->addColumn(
            'count_new_month',
            [
                'header' => __('New Subscriptions (Entire Month)'),
                'index' => 'count_new_month',
                'type' => 'number',
                'sortable' => false,
                'renderer' => 'Toppik\Report\Block\Adminhtml\Widget\Grid\Column\Renderer\Subscription\Monthly\YearRow',
                'header_css_class' => 'col-orders',
                'column_css_class' => 'col-orders'
            ]
        );
        
        $this->addColumn(
            'count_cancelled_month',
            [
                'header' => __('Cancelled (Entire Month)'),
                'index' => 'count_cancelled_month',
                'type' => 'number',
                'sortable' => false,
                'renderer' => 'Toppik\Report\Block\Adminhtml\Widget\Grid\Column\Renderer\Subscription\Monthly\YearRow',
                'header_css_class' => 'col-orders',
                'column_css_class' => 'col-orders'
            ]
        );
        
        $this->addColumn(
            'count_suspended_month',
            [
                'header' => __('Suspended (Entire Month)'),
                'index' => 'count_suspended_month',
                'type' => 'number',
                'sortable' => false,
                'renderer' => 'Toppik\Report\Block\Adminhtml\Widget\Grid\Column\Renderer\Subscription\Monthly\YearRow',
                'header_css_class' => 'col-orders',
                'column_css_class' => 'col-orders'
            ]
        );
        
        $this->addExportType('*/*/ExportPivotCsv', __('CSV'));
		
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
