<?php
namespace Toppik\Report\Block\Adminhtml\Subscription\Daily;

class Grid extends \Magento\Reports\Block\Adminhtml\Grid\AbstractGrid {
	
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
        parent::_construct();
    }
	
    /**
     * {@inheritdoc}
     */
    public function getResourceCollectionName() {
        return 'Toppik\Report\Model\ResourceModel\Report\Subscription\Daily\Collection';
    }
	
    /**
     * {@inheritdoc}
     *
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     */
    protected function _prepareColumns() {
        $this->addColumn(
            'date',
            [
                'header' => __('Date'),
                'index' => 'date',
                'type' => 'datetime',
                'renderer' => 'Magento\Reports\Block\Adminhtml\Sales\Grid\Column\Renderer\Date',
				'format' => 'MMM d, y',
                'sortable' => false,
                'header_css_class' => 'col-orders',
                'column_css_class' => 'col-orders'
            ]
        );
        
        $this->addColumn(
            'active',
            [
                'header' => __('Active (Toppik)'),
                'index' => 'active',
                'type' => 'number',
                'sortable' => false,
                'header_css_class' => 'col-orders',
                'column_css_class' => 'col-orders'
            ]
        );
        
        $this->addColumn(
            'active_ms',
            [
                'header' => __('Active (MS)'),
                'index' => 'active_ms',
                'type' => 'number',
                'sortable' => false,
                'header_css_class' => 'col-orders',
                'column_css_class' => 'col-orders'
            ]
        );
        
        $this->addColumn(
            'suspended',
            [
                'header' => __('Suspended (Toppik)'),
                'index' => 'suspended',
                'type' => 'number',
                'sortable' => false,
                'header_css_class' => 'col-orders',
                'column_css_class' => 'col-orders'
            ]
        );
        
        $this->addColumn(
            'suspended_ms',
            [
                'header' => __('Suspended (MS)'),
                'index' => 'suspended_ms',
                'type' => 'number',
                'sortable' => false,
                'header_css_class' => 'col-orders',
                'column_css_class' => 'col-orders'
            ]
        );
        
        $this->addColumn(
            'cancelled',
            [
                'header' => __('Cancelled (Toppik)'),
                'index' => 'cancelled',
                'type' => 'number',
                'sortable' => false,
                'header_css_class' => 'col-orders',
                'column_css_class' => 'col-orders'
            ]
        );
        
        $this->addColumn(
            'cancelled_ms',
            [
                'header' => __('Cancelled (MS)'),
                'index' => 'cancelled_ms',
                'type' => 'number',
                'sortable' => false,
                'header_css_class' => 'col-orders',
                'column_css_class' => 'col-orders'
            ]
        );
        
        $this->addExportType('*/*/ExportDailyCsv', __('CSV'));
		
        return parent::_prepareColumns();
    }
    
}
