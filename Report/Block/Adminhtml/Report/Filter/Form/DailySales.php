<?php
namespace Toppik\Report\Block\Adminhtml\Report\Filter\Form;

class DailySales extends \Magento\Sales\Block\Adminhtml\Report\Filter\Form
{

    /**
     * Customer Groups
     *
     * @var \Toppik\Report\Model\Sales\Source\CustomerGroupsFactory
     */
    protected $_customerGroupsFactory;

    /**
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\Framework\Data\FormFactory $formFactory
     * @param \Magento\Sales\Model\Order\ConfigFactory $orderConfig
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\Data\FormFactory $formFactory,
        \Magento\Sales\Model\Order\ConfigFactory $orderConfig,
        \Toppik\Report\Model\Sales\Source\CustomerGroupsFactory $customerGroupsFactory,
        array $data = []
    ) {
        $this->_customerGroupsFactory = $customerGroupsFactory;
        parent::__construct($context, $registry, $formFactory, $orderConfig, $data);
    }

    /**
     * Preparing form
     *
     * @return $this
     * @SuppressWarnings(PHPMD.UnusedLocalVariable)
     */
    protected function _prepareForm() {
        $this->_reportTypeOptions = ['date_range' => __('Date Range')];
        
        parent::_prepareForm();
        
        $form = $this->getForm();
        $htmlIdPrefix = $form->getHtmlIdPrefix();
        /** @var \Magento\Framework\Data\Form\Element\Fieldset $fieldset */
        $fieldset = $this->getForm()->getElement('base_fieldset');
        
        $fieldset->removeField('show_empty_rows');
        $fieldset->removeField('show_order_statuses');
        $fieldset->removeField('order_statuses');
        $fieldset->removeField('period_type');
        $fieldset->removeField('from');
        $fieldset->removeField('to');
        
        $dateFormat = $this->_localeDate->getDateFormat(\IntlDateFormatter::SHORT);
        
        $fieldset->addField(
            'from',
            'date',
            [
                'name' => 'from',
                'label' => __('From'),
                'title' => __('From'),
                'required' => true,
                'class' => 'admin__control-text',
                'date_format' => $dateFormat,
                'time_format' => 'HH:mm:ss'
            ]
        );
        
        $fieldset->addField(
            'to',
            'date',
            [
                'name' => 'to',
                'label' => __('To'),
                'title' => __('To'),
                'required' => true,
                'class' => 'admin__control-text',
                'date_format' => $dateFormat,
                'time_format' => 'HH:mm:ss'
            ]
        );

        $customerGroups = $this->_customerGroupsFactory->create();

        $fieldset->addField(
            'customer_group',
            'select',
            [
                'name' => 'customer_group',
                'label' => __('Customer Group'),
                'title' => __('Customer Group'),
                'values' => $customerGroups->toOptionArray()
            ]
        );

        $fieldset->addField(
            'grouping',
            'select',
            [
                'name' => 'grouping',
                'label' => __('Grouping'),
                'title' => __('Grouping'),
                'values' => [
                    __('No Grouping'),
                    ['label'=>'Daily Grouping','value'=>'daily'],
                    ['label'=>'Monthly Grouping','value'=>'monthly'],
                    ['label'=>'Quarterly','value'=>'quarterly'],
                    ['label'=>'Yearly','value'=>'yearly'],
                ]
            ]
        );


        
        $this->setChild(
            'form_after',
            $this->getLayout()->createBlock(
                'Magento\Backend\Block\Widget\Form\Element\Dependence'
            )->addFieldMap(
                "{$htmlIdPrefix}report_type",
                'report_type'
            )->addFieldMap(
                "{$htmlIdPrefix}from",
                'from'
            )->addFieldMap(
                "{$htmlIdPrefix}to",
                'to'
            )->addFieldDependence(
                'from',
                'report_type',
                'date_range'
            )->addFieldDependence(
                'to',
                'report_type',
                'date_range'
            )
        );
        
        $actionUrl = $this->getUrl('*/*/index');
        
        $form->setData('action', $actionUrl);
        
        return $this;
    }
    
}
