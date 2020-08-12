<?php
namespace Toppik\Report\Controller\Adminhtml\Subscription;

use Magento\Framework\App\Filesystem\DirectoryList;

class ExportMonthlySumCsv extends \Magento\Reports\Controller\Adminhtml\Report\Sales {
	
    /**
     * @param \Magento\Backend\App\Action\Context $context
     * @param \Magento\Framework\App\Response\Http\FileFactory $fileFactory
     * @param \Magento\Framework\Stdlib\DateTime\Filter\Date $dateFilter
     * @param TimezoneInterface $timezone
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Framework\App\Response\Http\FileFactory $fileFactory,
        \Magento\Framework\Stdlib\DateTime\Filter\Date $dateFilter,
        \Magento\Framework\Stdlib\DateTime\TimezoneInterface $timezone
    ) {
        parent::__construct($context, $fileFactory, $dateFilter, $timezone);
    }
	
    /**
     * Report action init operations
     *
     * @param array|\Magento\Framework\DataObject $blocks
     * @return $this
     */
    public function _initReportAction($blocks) {
        if(!is_array($blocks)) {
            $blocks = [$blocks];
        }
		
        foreach($blocks as $block) {
            if($block) {
                $block->setFilterData(new \Magento\Framework\DataObject());
            }
        }
		
        return $this;
    }
	
    /**
     * Export sales report grid to CSV format
     *
     * @return ResponseInterface
     */
    public function execute() {
        $fileName = 'toppikreport_subscription_monthly_sum.csv';
        $grid = $this->_view->getLayout()->createBlock('Toppik\Report\Block\Adminhtml\Subscription\Monthly\Sum\Grid');
        $this->_initReportAction($grid);
        return $this->_fileFactory->create($fileName, $grid->getCsvFile(), DirectoryList::VAR_DIR);
    }
	
}
