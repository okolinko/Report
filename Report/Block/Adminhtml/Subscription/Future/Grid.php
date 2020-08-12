<?php
namespace Toppik\Report\Block\Adminhtml\Subscription\Future;

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
        return 'Toppik\Report\Model\ResourceModel\Report\Subscription\Future\Collection';
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
                'type' => 'text',
                'renderer' => 'Toppik\Report\Block\Adminhtml\Widget\Grid\Column\Renderer\Subscription\Future\Date',
                'sortable' => false,
                'header_css_class' => 'col-orders',
                'column_css_class' => 'col-orders'
            ]
        );
        
        $this->addColumn(
            'subscription_count_toppik',
            [
                'header' => __('Subscription Count (Toppik)'),
                'index' => 'subscription_count_toppik',
                'type' => 'number',
                'sortable' => false,
                'renderer' => 'Toppik\Report\Block\Adminhtml\Widget\Grid\Column\Renderer\Subscription\Future\CountToppik',
                'header_css_class' => 'col-orders',
                'column_css_class' => 'col-orders'
            ]
        );
        
        $this->addColumn(
            'subscription_total_toppik',
            [
                'header' => __('Subscription Total (Toppik)'),
                'index' => 'subscription_total_toppik',
                'type' => 'number',
                'sortable' => false,
                'renderer' => 'Toppik\Report\Block\Adminhtml\Widget\Grid\Column\Renderer\Subscription\Future\TotalToppik',
                'header_css_class' => 'col-orders',
                'column_css_class' => 'col-orders'
            ]
        );
        
        $this->addColumn(
            'subscription_count_ms',
            [
                'header' => __('Subscription Count (MS)'),
                'index' => 'subscription_count_ms',
                'type' => 'number',
                'sortable' => false,
                'renderer' => 'Toppik\Report\Block\Adminhtml\Widget\Grid\Column\Renderer\Subscription\Future\CountMs',
                'header_css_class' => 'col-orders',
                'column_css_class' => 'col-orders'
            ]
        );
        
        $this->addColumn(
            'subscription_total_ms',
            [
                'header' => __('Subscription Total (MS)'),
                'index' => 'subscription_total_ms',
                'type' => 'number',
                'sortable' => false,
                'renderer' => 'Toppik\Report\Block\Adminhtml\Widget\Grid\Column\Renderer\Subscription\Future\TotalMs',
                'header_css_class' => 'col-orders',
                'column_css_class' => 'col-orders'
            ]
        );
        
        $this->addExportType('*/*/ExportFutureCsv', __('CSV'));
		
        return parent::_prepareColumns();
    }
    
}
