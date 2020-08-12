<?php
namespace Toppik\Report\Block\Adminhtml\Report;

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
    protected function _construct()
    {
        $this->setSaveParametersInSession(false);
        parent::_construct();
    }

    /**
     * {@inheritdoc}
     */
    public function getResourceCollectionName()
    {
        return 'Toppik\Report\Model\ResourceModel\Report\Collection';
    }

    /**
     * {@inheritdoc}
     *
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     */
    protected function _prepareColumns()
    {
        $this->addColumn(
            'customer_id',
            [
                'header' => __('Customer_ID'),
                'index' => 'customer_id',
                'type' => 'text',
                'sortable' => false,
                'header_css_class' => 'col-orders',
                'column_css_class' => 'col-orders'
            ]
        );

        $this->addColumn(
            'customer_email',
            [
                'header' => __('Customer_Email'),
                'index' => 'customer_email',
                'type' => 'text',
                'sortable' => false,
                'header_css_class' => 'col-orders',
                'column_css_class' => 'col-orders'
            ]
        );

        $this->addColumn(
            'customer_since_from',
            [
                'header' => __('Customer_Since'),
                'index' => 'customer_since_from',
                'type' => 'datetime',
                'sortable' => false,
                'header_css_class' => 'col-orders',
                'column_css_class' => 'col-orders'
            ]
        );

        $this->addColumn(
            'order_number',
            [
                'header' => __('Order_Number'),
                'index' => 'increment_id',
                'type' => 'text',
                'sortable' => false,
                'header_css_class' => 'col-orders',
                'column_css_class' => 'col-orders'
            ]
        );

        $this->addColumn(
            'order_status',
            [
                'header' => __('Order_Status'),
                'index' => 'order_status',
                'type' => 'text',
                'sortable' => false,
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
            'rep_code',
            [
                'header' => __('Rep Code'),
                'index' => 'rep_code',
                'type' => 'text',
                'sortable' => false,
                'header_css_class' => 'col-orders',
                'column_css_class' => 'col-orders'
            ]
        );

        $this->addColumn(
            'ship_by',
            [
                'header' => __('Ship_By'),
                'index' => 'ship_by',
                'type' => 'text',
                'sortable' => false,
                'header_css_class' => 'col-orders',
                'column_css_class' => 'col-orders'
            ]
        );

        $this->addColumn(
            'Ship_to_Name',
            [
                'header' => __('Ship_to_Name'),
                'index' => 'company',
                'type' => 'text',
                'sortable' => false,
                'header_css_class' => 'col-orders',
                'column_css_class' => 'col-orders'
            ]
        );

        $this->addColumn(
            'customer_group_label',
            [
                'header' => __('Customer Group'),
                'index' => 'customer_group_label',
                'type' => 'text',
                'sortable' => false,
                'header_css_class' => 'col-orders',
                'column_css_class' => 'col-orders'
            ]
        );

        $this->addColumn(
            'First_Name',
            [
                'header' => __('First_Name'),
                'index' => 'customer_firstname',
                'type' => 'text',
                'sortable' => false,
                'header_css_class' => 'col-orders',
                'column_css_class' => 'col-orders'
            ]
        );

        $this->addColumn(
            'Last_Name',
            [
                'header' => __('Last_Name'),
                'index' => 'customer_lastname',
                'type' => 'text',
                'sortable' => false,
                'header_css_class' => 'col-orders',
                'column_css_class' => 'col-orders'
            ]
        );

        $this->addColumn(
            'Ship_to_Address_Line_One',
            [
                'header' => __('Ship_to_Address_Line_One'),
                'index' => 'street_1',
                'type' => 'text',
                'sortable' => false,
                'header_css_class' => 'col-orders',
                'column_css_class' => 'col-orders'
            ]
        );

        $this->addColumn(
            'Ship_to_Address_Line_Two',
            [
                'header' => __('Ship_to_Address_Line_Two'),
                'index' => 'street_2',
                'type' => 'text',
                'sortable' => false,
                'header_css_class' => 'col-orders',
                'column_css_class' => 'col-orders'
            ]
        );

        $this->addColumn(
            'Ship_to_Address_Line_Three',
            [
                'header' => __('Ship_to_Address_Line_Three'),
                'index' => 'street_3',
                'type' => 'text',
                'sortable' => false,
                'header_css_class' => 'col-orders',
                'column_css_class' => 'col-orders'
            ]
        );

        $this->addColumn(
            'Ship_to_City',
            [
                'header' => __('Ship_to_City'),
                'index' => 'city',
                'type' => 'text',
                'sortable' => false,
                'header_css_class' => 'col-orders',
                'column_css_class' => 'col-orders'
            ]
        );

        $this->addColumn(
            'Ship_to_State',
            [
                'header' => __('Ship_to_State'),
                'index' => 'region',
                'type' => 'text',
                'sortable' => false,
                'header_css_class' => 'col-orders',
                'column_css_class' => 'col-orders'
            ]
        );

        $this->addColumn(
            'Ship_to_Zipcode',
            [
                'header' => __('Ship_to_Zipcode'),
                'index' => 'postcode',
                'type' => 'text',
                'sortable' => false,
                'header_css_class' => 'col-orders',
                'column_css_class' => 'col-orders'
            ]
        );

        $this->addColumn(
            'Ship_to_Country',
            [
                'header' => __('Ship_to_Country'),
                'index' => 'country_id',
                'type' => 'text',
                'sortable' => false,
                'header_css_class' => 'col-orders',
                'column_css_class' => 'col-orders'
            ]
        );

        $this->addColumn(
            'Phone',
            [
                'header' => __('Phone'),
                'index' => 'telephone',
                'type' => 'text',
                'sortable' => false,
                'header_css_class' => 'col-orders',
                'column_css_class' => 'col-orders'
            ]
        );
        
        $this->addColumn(
            'billing_street_1',
            [
                'header' => __('Bill_to_Address_Line_One'),
                'index' => 'billing_street_1',
                'type' => 'text',
                'sortable' => false,
                'header_css_class' => 'col-orders',
                'column_css_class' => 'col-orders'
            ]
        );

        $this->addColumn(
            'billing_street_2',
            [
                'header' => __('Bill_to_Address_Line_Two'),
                'index' => 'billing_street_2',
                'type' => 'text',
                'sortable' => false,
                'header_css_class' => 'col-orders',
                'column_css_class' => 'col-orders'
            ]
        );

        $this->addColumn(
            'billing_street_3',
            [
                'header' => __('Bill_to_Address_Line_Three'),
                'index' => 'billing_street_3',
                'type' => 'text',
                'sortable' => false,
                'header_css_class' => 'col-orders',
                'column_css_class' => 'col-orders'
            ]
        );

        $this->addColumn(
            'billing_city',
            [
                'header' => __('Bill_to_City'),
                'index' => 'billing_city',
                'type' => 'text',
                'sortable' => false,
                'header_css_class' => 'col-orders',
                'column_css_class' => 'col-orders'
            ]
        );

        $this->addColumn(
            'billing_region',
            [
                'header' => __('Bill_to_State'),
                'index' => 'billing_region',
                'type' => 'text',
                'sortable' => false,
                'header_css_class' => 'col-orders',
                'column_css_class' => 'col-orders'
            ]
        );

        $this->addColumn(
            'billing_postcode',
            [
                'header' => __('Bill_to_Zipcode'),
                'index' => 'billing_postcode',
                'type' => 'text',
                'sortable' => false,
                'header_css_class' => 'col-orders',
                'column_css_class' => 'col-orders'
            ]
        );

        $this->addColumn(
            'billing_country_id',
            [
                'header' => __('Bill_to_Country'),
                'index' => 'billing_country_id',
                'type' => 'text',
                'sortable' => false,
                'header_css_class' => 'col-orders',
                'column_css_class' => 'col-orders'
            ]
        );

        $this->addColumn(
            'billing_telephone',
            [
                'header' => __('Bill Phone'),
                'index' => 'billing_telephone',
                'type' => 'text',
                'sortable' => false,
                'header_css_class' => 'col-orders',
                'column_css_class' => 'col-orders'
            ]
        );
        
        $this->addColumn(
            'Customer_PO',
            [
                'header' => __('Customer_PO'),
                'index' => 'po_number',
                'type' => 'text',
                'sortable' => false,
                'header_css_class' => 'col-orders',
                'column_css_class' => 'col-orders'
            ]
        );

        $this->addColumn(
            'Ship_Date',
            [
                'header' => __('Ship_Date'),
                'index' => 'shipped_at',
                'type' => 'text',
                'sortable' => false,
                'renderer' => 'Magento\Reports\Block\Adminhtml\Sales\Grid\Column\Renderer\Date',
				'format' => 'MMM d, y HH:mm:ss',
                'header_css_class' => 'col-orders',
                'column_css_class' => 'col-orders'
            ]
        );

        $this->addColumn(
            'Quantity',
            [
                'header' => __('Quantity'),
                'index' => 'qty_ordered',
                'type' => 'number',
                'sortable' => false,
                'header_css_class' => 'col-orders',
                'column_css_class' => 'col-orders'
            ]
        );

        $this->addColumn(
            'Item_ID',
            [
                'header' => __('Item_ID'),
                'index' => 'sku',
                'type' => 'text',
                'sortable' => false,
                'header_css_class' => 'col-orders',
                'column_css_class' => 'col-orders'
            ]
        );

        $this->addColumn(
            'Description',
            [
                'header' => __('Description'),
                'index' => 'name',
                'type' => 'text',
                'sortable' => false,
                'header_css_class' => 'col-orders',
                'column_css_class' => 'col-orders'
            ]
        );

        $this->addColumn(
            'Unit_Price',
            [
                'header' => __('Unit_Price'),
                'index' => 'discounted_price',
                'type' => 'text',
                'sortable' => false,
                'header_css_class' => 'col-orders',
                'column_css_class' => 'col-orders'
            ]
        );

        $this->addColumn(
            'MerchandiseTotal',
            [
                'header' => __('MerchandiseTotal'),
                'index' => 'discounted_row_total',
                'type' => 'text',
                'sortable' => false,
                'header_css_class' => 'col-orders',
                'column_css_class' => 'col-orders'
            ]
        );

        $this->addColumn(
            'Order_Total',
            [
                'header' => __('Order_Total'),
                'index' => 'order_total',
                'type' => 'text',
                'sortable' => false,
                'header_css_class' => 'col-orders',
                'column_css_class' => 'col-orders'
            ]
        );

        $this->addColumn(
            'UOM_ID',
            [
                'header' => __('UOM_ID'),
                'index' => 'uom_id',
                'type' => 'text',
                'sortable' => false,
                'header_css_class' => 'col-orders',
                'column_css_class' => 'col-orders'
            ]
        );

        $this->addColumn(
            'Payment_Type',
            [
                'header' => __('Payment_Type'),
                'index' => 'payment_type',
                'type' => 'text',
                'sortable' => false,
                'header_css_class' => 'col-orders',
                'column_css_class' => 'col-orders'
            ]
        );

        $this->addColumn(
            'Promotion_Code',
            [
                'header' => __('Promotion_Code'),
                'index' => 'discount_description',
                'type' => 'text',
                'sortable' => false,
                'header_css_class' => 'col-orders',
                'column_css_class' => 'col-orders'
            ]
        );

        $this->addColumn(
            'Item_Sales_Tax',
            [
                'header' => __('Item_Sales_Tax'),
                'index' => 'tax_amount',
                'type' => 'text',
                'sortable' => false,
                'header_css_class' => 'col-orders',
                'column_css_class' => 'col-orders'
            ]
        );

        $this->addColumn(
            'Shipping',
            [
                'header' => __('Shipping'),
                'index' => 'shipping_amount',
                'type' => 'text',
                'sortable' => false,
                'header_css_class' => 'col-orders',
                'column_css_class' => 'col-orders'
            ]
        );

        $this->addColumn(
            'Member_Type',
            [
                'header' => __('Member_Type'),
                'index' => 'member_type_value',
                'type' => 'text',
                'sortable' => false,
                'header_css_class' => 'col-orders',
                'column_css_class' => 'col-orders'
            ]
        );

        $this->addColumn(
            'CustomerClass',
            [
                'header' => __('CustomerClass'),
                'index' => 'customer_class_value',
                'type' => 'text',
                'sortable' => false,
                'header_css_class' => 'col-orders',
                'column_css_class' => 'col-orders'
            ]
        );

        $this->addColumn(
            'Shipment_Tracking_Info',
            [
                'header' => __('Shipment_Tracking_Info'),
                'index' => 'track_number',
                'type' => 'text',
                'sortable' => false,
                'header_css_class' => 'col-orders',
                'column_css_class' => 'col-orders'
            ]
        );

        $this->addColumn(
            'Ship_Method',
            [
                'header' => __('Ship_Method'),
                'index' => 'shipping_method',
                'type' => 'text',
                'sortable' => false,
                'header_css_class' => 'col-orders',
                'column_css_class' => 'col-orders',
                'renderer'  => 'Toppik\Report\Block\Adminhtml\Report\Column\Renderer\ShipMethod'
            ]
        );

        $this->addColumn(
            'Order_Source',
            [
                'header' => __('Order_Source'),
                'index' => 'source',
                'type' => 'text',
                'sortable' => false,
                'header_css_class' => 'col-orders',
                'column_css_class' => 'col-orders'
            ]
        );

        $this->addColumn(
            'External_Order_Reference',
            [
                'header' => __('External_Order_Reference'),
                'index' => 'increment_id',
                'type' => 'text',
                'sortable' => false,
                'header_css_class' => 'col-orders',
                'column_css_class' => 'col-orders'
            ]
        );

        $this->addColumn(
            'Merchant_Order_Source',
            [
                'header' => __('Merchant_Order_Source'),
                'index' => 'merchant_source',
                'type' => 'text',
                'sortable' => false,
                'header_css_class' => 'col-orders',
                'column_css_class' => 'col-orders'
            ]
        );

        $this->addColumn(
            'Credit_Card_Payment_Date',
            [
                'header' => __('Credit_Card_Payment_Date'),
                'index' => 'payment_date',
                'type' => 'text',
                'sortable' => false,
                'header_css_class' => 'col-orders',
                'column_css_class' => 'col-orders'
            ]
        );

        $this->addColumn(
            'Shipping_Handling_Tax',
            [
                'header' => __('Shipping_Handling_Tax'),
                'index' => 'shipping_tax_amount',
                'type' => 'text',
                'sortable' => false,
                'header_css_class' => 'col-orders',
                'column_css_class' => 'col-orders'
            ]
        );

        $this->addExportType('*/*/exportSalesCsv', __('CSV'));
		
        return parent::_prepareColumns();
    }
}
