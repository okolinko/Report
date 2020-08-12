<?php
namespace Toppik\Report\Block\Adminhtml\Sales;

class Daily extends \Magento\Backend\Block\Widget\Grid\Container
{

    /**
     * Template file
     *
     * @var string
     */
    protected $_template = 'Magento_Reports::report/grid/container.phtml';

    /**
     * {@inheritdoc}
     */
    protected function _construct() {
        $this->_blockGroup = 'Toppik_Report';
        $this->_controller = 'adminhtml_sales_daily';
        $this->_headerText = __('Total Report');
        parent::_construct();

        $this->buttonList->remove('add');

        $this->addButton(
            'filter_form_submit',
            ['label' => __('Show Report'), 'onclick' => 'filterFormSubmit()', 'class' => 'primary']
        );
    }

    /**
     * Get filter URL
     *
     * @return string
     */
    public function getFilterUrl() {
        $this->getRequest()->setParam('filter', null);
        return $this->getUrl('*/*/daily', ['_current' => true]);
    }

}
