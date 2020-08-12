<?php
namespace Toppik\Report\Block\Adminhtml\Report\Filter\Form;

class DateTimeRange extends \Magento\Sales\Block\Adminhtml\Report\Filter\Form {
    
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
