<?php
namespace Toppik\Report\Block\Adminhtml\Subscription\Index;

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
        return 'Toppik\Report\Model\ResourceModel\Report\Subscription\Index\Collection';
    }
	
    /**
     * {@inheritdoc}
     *
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     */
    protected function _prepareColumns() {
        $this->addColumn(
            'period_from_original',
            [
                'header' => __('Period From'),
                'index' => 'period_from_original',
                'type' => 'datetime',
                'renderer' => 'Magento\Reports\Block\Adminhtml\Sales\Grid\Column\Renderer\Date',
				'format' => 'MMM d, y HH:mm:ss',
                'sortable' => false,
                'header_css_class' => 'col-orders',
                'column_css_class' => 'col-orders'
            ]
        );
		
        $this->addColumn(
            'period_to_original',
            [
                'header' => __('Period To'),
                'index' => 'period_to_original',
                'type' => 'datetime',
                'renderer' => 'Magento\Reports\Block\Adminhtml\Sales\Grid\Column\Renderer\Date',
				'format' => 'MMM d, y HH:mm:ss',
                'sortable' => false,
                'header_css_class' => 'col-orders',
                'column_css_class' => 'col-orders'
            ]
        );
        
        $this->addColumn(
            'count_orders',
            [
                'header' => __('# Of Created Orders'),
                'index' => 'count_orders',
                'type' => 'number',
                'sortable' => false,
                'header_css_class' => 'col-orders',
                'column_css_class' => 'col-orders'
            ]
        );
        
        $this->addColumn(
            'sum_orders',
            [
                'header' => __('Total $ Value Of Created Orders'),
                'index' => 'sum_orders',
                'type' => 'number',
                'sortable' => false,
                'header_css_class' => 'col-orders',
                'column_css_class' => 'col-orders'
            ]
        );
        
        $this->addColumn(
            'active_t_count_from',
            [
                'header' => __('# Of Active (Toppik) At Start Of Period'),
                'index' => 'active_t_count_from',
                'type' => 'number',
                'sortable' => false,
                'renderer' => 'Toppik\Report\Block\Adminhtml\Widget\Grid\Column\Renderer\Subscription\Index\CountActiveToppik',
                'header_css_class' => 'col-orders',
                'column_css_class' => 'col-orders'
            ]
        );
        
        $this->addColumn(
            'active_t_sum_from',
            [
                'header' => __('Sum Of Active (Toppik) At Start Of Period'),
                'index' => 'active_t_sum_from',
                'type' => 'number',
                'sortable' => false,
                'renderer' => 'Toppik\Report\Block\Adminhtml\Widget\Grid\Column\Renderer\Subscription\Index\TotalActiveToppik',
                'header_css_class' => 'col-orders',
                'column_css_class' => 'col-orders'
            ]
        );
        
        $this->addColumn(
            'active_ms_count_from',
            [
                'header' => __('# Of Active (MS) At Start Of Period'),
                'index' => 'active_ms_count_from',
                'type' => 'number',
                'sortable' => false,
                'renderer' => 'Toppik\Report\Block\Adminhtml\Widget\Grid\Column\Renderer\Subscription\Index\CountActiveMs',
                'header_css_class' => 'col-orders',
                'column_css_class' => 'col-orders'
            ]
        );
        
        $this->addColumn(
            'active_ms_sum_from',
            [
                'header' => __('Sum Of Active (MS) At Start Of Period'),
                'index' => 'active_ms_sum_from',
                'type' => 'number',
                'sortable' => false,
                'renderer' => 'Toppik\Report\Block\Adminhtml\Widget\Grid\Column\Renderer\Subscription\Index\TotalActiveMs',
                'header_css_class' => 'col-orders',
                'column_css_class' => 'col-orders'
            ]
        );
        
        $this->addColumn(
            'active_t_count_to',
            [
                'header' => __('# Of Active (Toppik) At End Of Period'),
                'index' => 'active_t_count_to',
                'type' => 'number',
                'sortable' => false,
                'renderer' => 'Toppik\Report\Block\Adminhtml\Widget\Grid\Column\Renderer\Subscription\Index\CountActiveToppik',
                'header_css_class' => 'col-orders',
                'column_css_class' => 'col-orders'
            ]
        );
        
        $this->addColumn(
            'active_t_sum_to',
            [
                'header' => __('Sum Of Active (Toppik) At End Of Period'),
                'index' => 'active_t_sum_to',
                'type' => 'number',
                'sortable' => false,
                'renderer' => 'Toppik\Report\Block\Adminhtml\Widget\Grid\Column\Renderer\Subscription\Index\TotalActiveToppik',
                'header_css_class' => 'col-orders',
                'column_css_class' => 'col-orders'
            ]
        );
        
        $this->addColumn(
            'active_ms_count_to',
            [
                'header' => __('# Of Active (MS) At End Of Period'),
                'index' => 'active_ms_count_to',
                'type' => 'number',
                'sortable' => false,
                'renderer' => 'Toppik\Report\Block\Adminhtml\Widget\Grid\Column\Renderer\Subscription\Index\CountActiveMs',
                'header_css_class' => 'col-orders',
                'column_css_class' => 'col-orders'
            ]
        );
        
        $this->addColumn(
            'active_ms_sum_to',
            [
                'header' => __('Sum Of Active (MS) At End Of Period'),
                'index' => 'active_ms_sum_to',
                'type' => 'number',
                'sortable' => false,
                'renderer' => 'Toppik\Report\Block\Adminhtml\Widget\Grid\Column\Renderer\Subscription\Index\TotalActiveMs',
                'header_css_class' => 'col-orders',
                'column_css_class' => 'col-orders'
            ]
        );
        
        $this->addColumn(
            'cancelled_t_count_from',
            [
                'header' => __('# Of Cancelled (Toppik) At Start Of Period'),
                'index' => 'cancelled_t_count_from',
                'type' => 'number',
                'sortable' => false,
                'renderer' => 'Toppik\Report\Block\Adminhtml\Widget\Grid\Column\Renderer\Subscription\Index\CountCancelledToppik',
                'header_css_class' => 'col-orders',
                'column_css_class' => 'col-orders'
            ]
        );
        
        $this->addColumn(
            'cancelled_t_sum_from',
            [
                'header' => __('Sum Of Cancelled (Toppik) At Start Of Period'),
                'index' => 'cancelled_t_sum_from',
                'type' => 'number',
                'sortable' => false,
                'renderer' => 'Toppik\Report\Block\Adminhtml\Widget\Grid\Column\Renderer\Subscription\Index\TotalCancelledToppik',
                'header_css_class' => 'col-orders',
                'column_css_class' => 'col-orders'
            ]
        );
        
        $this->addColumn(
            'cancelled_ms_count_from',
            [
                'header' => __('# Of Cancelled (MS) At Start Of Period'),
                'index' => 'cancelled_ms_count_from',
                'type' => 'number',
                'sortable' => false,
                'renderer' => 'Toppik\Report\Block\Adminhtml\Widget\Grid\Column\Renderer\Subscription\Index\CountCancelledMs',
                'header_css_class' => 'col-orders',
                'column_css_class' => 'col-orders'
            ]
        );
        
        $this->addColumn(
            'cancelled_ms_sum_from',
            [
                'header' => __('Sum Of Cancelled (MS) At Start Of Period'),
                'index' => 'cancelled_ms_sum_from',
                'type' => 'number',
                'sortable' => false,
                'renderer' => 'Toppik\Report\Block\Adminhtml\Widget\Grid\Column\Renderer\Subscription\Index\TotalCancelledMs',
                'header_css_class' => 'col-orders',
                'column_css_class' => 'col-orders'
            ]
        );
        
        $this->addColumn(
            'cancelled_t_count_to',
            [
                'header' => __('# Of Cancelled (Toppik) At End Of Period'),
                'index' => 'cancelled_t_count_to',
                'type' => 'number',
                'sortable' => false,
                'renderer' => 'Toppik\Report\Block\Adminhtml\Widget\Grid\Column\Renderer\Subscription\Index\CountCancelledToppik',
                'header_css_class' => 'col-orders',
                'column_css_class' => 'col-orders'
            ]
        );
        
        $this->addColumn(
            'cancelled_t_sum_to',
            [
                'header' => __('Sum Of Cancelled (Toppik) At End Of Period'),
                'index' => 'cancelled_t_sum_to',
                'type' => 'number',
                'sortable' => false,
                'renderer' => 'Toppik\Report\Block\Adminhtml\Widget\Grid\Column\Renderer\Subscription\Index\TotalCancelledToppik',
                'header_css_class' => 'col-orders',
                'column_css_class' => 'col-orders'
            ]
        );
        
        $this->addColumn(
            'cancelled_ms_count_to',
            [
                'header' => __('# Of Cancelled (MS) At End Of Period'),
                'index' => 'cancelled_ms_count_to',
                'type' => 'number',
                'sortable' => false,
                'renderer' => 'Toppik\Report\Block\Adminhtml\Widget\Grid\Column\Renderer\Subscription\Index\CountCancelledMs',
                'header_css_class' => 'col-orders',
                'column_css_class' => 'col-orders'
            ]
        );
        
        $this->addColumn(
            'cancelled_ms_sum_to',
            [
                'header' => __('Sum Of Cancelled (MS) At End Of Period'),
                'index' => 'cancelled_ms_sum_to',
                'type' => 'number',
                'sortable' => false,
                'renderer' => 'Toppik\Report\Block\Adminhtml\Widget\Grid\Column\Renderer\Subscription\Index\TotalCancelledMs',
                'header_css_class' => 'col-orders',
                'column_css_class' => 'col-orders'
            ]
        );
        
        $this->addColumn(
            'suspended_t_count_from',
            [
                'header' => __('# Of Suspended (Toppik) At Start Of Period'),
                'index' => 'suspended_t_count_from',
                'type' => 'number',
                'sortable' => false,
                'renderer' => 'Toppik\Report\Block\Adminhtml\Widget\Grid\Column\Renderer\Subscription\Index\CountSuspendedToppik',
                'header_css_class' => 'col-orders',
                'column_css_class' => 'col-orders'
            ]
        );
        
        $this->addColumn(
            'suspended_t_sum_from',
            [
                'header' => __('Sum Of Suspended (Toppik) At Start Of Period'),
                'index' => 'suspended_t_sum_from',
                'type' => 'number',
                'sortable' => false,
                'renderer' => 'Toppik\Report\Block\Adminhtml\Widget\Grid\Column\Renderer\Subscription\Index\TotalSuspendedToppik',
                'header_css_class' => 'col-orders',
                'column_css_class' => 'col-orders'
            ]
        );
        
        $this->addColumn(
            'suspended_ms_count_from',
            [
                'header' => __('# Of Suspended (MS) At Start Of Period'),
                'index' => 'suspended_ms_count_from',
                'type' => 'number',
                'sortable' => false,
                'renderer' => 'Toppik\Report\Block\Adminhtml\Widget\Grid\Column\Renderer\Subscription\Index\CountSuspendedMs',
                'header_css_class' => 'col-orders',
                'column_css_class' => 'col-orders'
            ]
        );
        
        $this->addColumn(
            'suspended_ms_sum_from',
            [
                'header' => __('Sum Of Suspended (MS) At Start Of Period'),
                'index' => 'suspended_ms_sum_from',
                'type' => 'number',
                'sortable' => false,
                'renderer' => 'Toppik\Report\Block\Adminhtml\Widget\Grid\Column\Renderer\Subscription\Index\TotalSuspendedMs',
                'header_css_class' => 'col-orders',
                'column_css_class' => 'col-orders'
            ]
        );
        
        $this->addColumn(
            'suspended_t_count_to',
            [
                'header' => __('# Of Suspended (Toppik) At End Of Period'),
                'index' => 'suspended_t_count_to',
                'type' => 'number',
                'sortable' => false,
                'renderer' => 'Toppik\Report\Block\Adminhtml\Widget\Grid\Column\Renderer\Subscription\Index\CountSuspendedToppik',
                'header_css_class' => 'col-orders',
                'column_css_class' => 'col-orders'
            ]
        );
        
        $this->addColumn(
            'suspended_t_sum_to',
            [
                'header' => __('Sum Of Suspended (Toppik) At End Of Period'),
                'index' => 'suspended_t_sum_to',
                'type' => 'number',
                'sortable' => false,
                'renderer' => 'Toppik\Report\Block\Adminhtml\Widget\Grid\Column\Renderer\Subscription\Index\TotalSuspendedToppik',
                'header_css_class' => 'col-orders',
                'column_css_class' => 'col-orders'
            ]
        );
        
        $this->addColumn(
            'suspended_ms_count_to',
            [
                'header' => __('# Of Suspended (MS) At End Of Period'),
                'index' => 'suspended_ms_count_to',
                'type' => 'number',
                'sortable' => false,
                'renderer' => 'Toppik\Report\Block\Adminhtml\Widget\Grid\Column\Renderer\Subscription\Index\CountSuspendedMs',
                'header_css_class' => 'col-orders',
                'column_css_class' => 'col-orders'
            ]
        );
        
        $this->addColumn(
            'suspended_ms_sum_to',
            [
                'header' => __('Sum Of Suspended (MS) At End Of Period'),
                'index' => 'suspended_ms_sum_to',
                'type' => 'number',
                'sortable' => false,
                'renderer' => 'Toppik\Report\Block\Adminhtml\Widget\Grid\Column\Renderer\Subscription\Index\TotalSuspendedMs',
                'header_css_class' => 'col-orders',
                'column_css_class' => 'col-orders'
            ]
        );
        
        $this->addExportType('*/*/ExportIndexCsv', __('CSV'));
		
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
