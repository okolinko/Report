<?php
namespace Toppik\Report\Block\Adminhtml\Report\Filter\Form;

class Order extends \Magento\Sales\Block\Adminhtml\Report\Filter\Form
{
    /**
     * Preparing form
     *
     * @return $this
     * @SuppressWarnings(PHPMD.UnusedLocalVariable)
     */
    protected function _prepareForm()
    {
        $this->_reportTypeOptions = ['created_at_order' => __('Date Range'), 'order_number' => __('Order #')];

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
            'number_from',
            'text',
            [
                'name' => 'number_from',
                'label' => __('From'),
                'title' => __('From'),
                'required' => true,
                'class' => 'admin__control-text'
            ]
        );

        $fieldset->addField(
            'number_to',
            'text',
            [
                'name' => 'number_to',
                'label' => __('To'),
                'title' => __('To'),
                'required' => true,
                'class' => 'admin__control-text'
            ]
        );

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

        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $orderStatuses = $objectManager->create('\Magento\Sales\Model\ResourceModel\Order\Status\Collection');
        $customerGroups = $objectManager->create('\Magento\Customer\Model\ResourceModel\Group\Collection');
        
        $fieldset->addField(
            'status',
            'select',
            [
                'name' => 'status',
                'label' => __('Status'),
                'title' => __('Status'),
                'values' => array(__('All statuses')) + $orderStatuses->toOptionArray()
            ]
        );
		
        $fieldset->addField(
            'customer_group_ids',
            'multiselect',
            [
                'name' => 'customer_group_ids',
                'label' => __('Customer Group IDs'),
                'title' => __('Customer Group IDs'),
                'values' => $customerGroups->toOptionArray()
            ]
        );
		
        $fieldset->addField(
            'product_sku',
            'text',
            [
                'name' => 'product_sku',
                'label' => __('Product Sku'),
                'title' => __('Product Sku')
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
            )->addFieldMap(
                "{$htmlIdPrefix}number_from",
                'number_from'
            )->addFieldMap(
                "{$htmlIdPrefix}number_to",
                'number_to'
            )->addFieldDependence(
                'from',
                'report_type',
                'created_at_order'
            )->addFieldDependence(
                'to',
                'report_type',
                'created_at_order'
            )->addFieldDependence(
                'number_from',
                'report_type',
                'order_number'
            )->addFieldDependence(
                'number_to',
                'report_type',
                'order_number'
            )
        );

        $actionUrl = $this->getUrl('*/*/index');

        $form->setData('action', $actionUrl);

        return $this;
    }
}
