<?php
namespace Toppik\Report\Block\Adminhtml\Subscription\Lifetime;

class Grid extends \Magento\Backend\Block\Widget\Grid\Extended {
    
    /**
     * Subscription grid collection
     *
     * @var \Toppik\Report\Model\ResourceModel\Report\Subscription\Lifetime\CollectionFactory
     */
    protected $_collectionFactory;

    /**
     * Subscription model
     *
     * @var \Toppik\Subscriptions\Model\Profile
     */
    protected $_profileFactory;

    /**
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Backend\Helper\Data $backendHelper
     * @param \Toppik\Report\Model\ResourceModel\Report\Subscription\Lifetime\CollectionFactory $collectionFactory
     * @param \Toppik\Subscriptions\Model\Profile $profileFactory
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Backend\Helper\Data $backendHelper,
        \Toppik\Report\Model\ResourceModel\Report\Subscription\Lifetime\CollectionFactory $collectionFactory,
        \Toppik\Subscriptions\Model\Profile $profileFactory,
        array $data = []
    ) {
        $this->_collectionFactory = $collectionFactory;
        $this->_profileFactory = $profileFactory;
        parent::__construct($context, $backendHelper, $data);
    }
    
    /**
     * Prepare related item collection
     *
     * @return \Toppik\Subscriptions\Block\Adminhtml\Customer\Edit\Tab\Grid
     */
    protected function _prepareCollection() {
        $this->_beforePrepareCollection();
        return parent::_prepareCollection();
    }
    
    /**
     * Configuring and setting collection
     *
     * @return $this
     */
    protected function _beforePrepareCollection() {
        if (!$this->getCollection()) {
            $collection = $this->_collectionFactory->create();
            $this->setCollection($collection);
        }
        return $this;
    }
    
    /**
     * Prepare grid columns
     *
     * @return \Toppik\Subscriptions\Block\Adminhtml\Customer\Edit\Tab\Grid
     */
    protected function _prepareColumns()
    {

        $this->addColumn(
            'profile_id',
            [
                'header' => __('ID'),
                'index' => 'profile_id',
                'type' => 'number'
            ]
        );

        $this->addColumn(
            'customer_id',
            [
                'header' => __('Customer ID'),
                'index' => 'customer_id',
                'type' => 'number'
            ]
        );

        $this->addColumn(
            'customer_email',
            [
                'header' => __('Customer Email'),
                'index' => 'customer_email',
                'type' => 'text'
            ]
        );

        $this->addColumn(
            'sku',
            [
                'header' => __('SKU'),
                'index' => 'sku',
                'type' => 'text'
            ]
        );

        $this->addColumn(
            'status',
            [
                'header' => __('Status'),
                'index' => 'status',
                'type' => 'options',
                'options' => $this->_profileFactory->getAvailableStatuses(),
                'header_css_class' => 'col-status',
                'column_css_class' => 'col-status'
            ]
        );


        $this->addColumn(
            'frequency_title',
            [
                'header' => __('Subscription Term'),
                'index' => 'frequency_title',
                'type' => 'text'
            ]
        );

        $this->addColumn(
            'grand_total',
            [
                'header' => __('Grand Total'),
                'index' => 'grand_total',
                'type' => 'text'
            ]
        );


        $this->addColumn(
            'merchant_source',
            [
                'header' => __('Merchant Source'),
                'index' => 'merchant_source',
                'type' => 'text'
            ]
        );

        $this->addColumn(
            'created_at',
            [
                'header' => __('Created At'),
                'index' => 'created_at',
                'type' => 'datetime',
                'html_decorators' => ['nobr'],
                'header_css_class' => 'col-period',
                'column_css_class' => 'col-period'
            ]
        );

        $this->addColumn(
            'start_date',
            [
                'header' => __('Start Date'),
                'index' => 'start_date',
                'type' => 'datetime',
                'html_decorators' => ['nobr'],
                'header_css_class' => 'col-period',
                'column_css_class' => 'col-period'
            ]
        );

        $this->addColumn(
            'cancelled_at',
            [
                'header' => __('Cancelled At'),
                'index' => 'cancelled_at',
                'type' => 'datetime',
                'html_decorators' => ['nobr'],
                'header_css_class' => 'col-period',
                'column_css_class' => 'col-period'
            ]
        );



        $this->addColumn(
            'suspended_at',
            [
                'header' => __('Suspended At'),
                'index' => 'suspended_at',
                'type' => 'datetime',
                'html_decorators' => ['nobr'],
                'header_css_class' => 'col-period',
                'column_css_class' => 'col-period'
            ]
        );

        $this->addColumn(
            'last_order_at',
            [
                'header' => __('Last Order Date'),
                'index' => 'last_order_at',
                'type' => 'datetime',
                'html_decorators' => ['nobr'],
                'header_css_class' => 'col-period',
                'column_css_class' => 'col-period'
            ]
        );

        $this->addColumn(
            'count_orders',

            [
                'header' => __('Count Orders'),
                'index' => 'count_orders',
                'type' => 'number'
            ]
        );

        $this->addColumn(
            'lifetime_value',
            [
                'header' => __('Lifetime Value'),
                'index' => 'lifetime_value',
                'type' => 'text',
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
            'last_suspend_error',
            [
                'header' => __('Last Suspend Error'),
                'index' => 'last_suspend_error',
                'type' => 'text',
            ]
        );




        $this->addExportType('*/*/ExportLifetimeCsv', __('CSV'));

    }
    
}
