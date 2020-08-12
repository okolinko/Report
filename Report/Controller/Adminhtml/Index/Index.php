<?php
namespace Toppik\Report\Controller\Adminhtml\Index;

use Magento\Framework\Controller\ResultFactory;
use Magento\Reports\Model\Flag;

class Index extends \Toppik\Report\Controller\Adminhtml\Index
{
    /**
     * Sales report action
     *
     * @return void
     */
    public function execute()
    {
        $this->_initAction()->_setActiveMenu(
            'Toppik_Report::toppik_report'
        )->_addBreadcrumb(
            __('Toppik Report'),
            __('Toppik Report')
        );
        $this->_view->getPage()->getConfig()->getTitle()->prepend(__('Toppik Report'));

        $gridBlock = $this->_view->getLayout()->getBlock('adminhtml_report.grid');
        $filterFormBlock = $this->_view->getLayout()->getBlock('grid.filter.form');

        $this->_initReportAction([$gridBlock, $filterFormBlock]);

        $this->_view->renderLayout();
    }
}
