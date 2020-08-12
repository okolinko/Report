<?php
namespace Toppik\Report\Controller\Adminhtml\Subscription;

use Magento\Framework\App\Filesystem\DirectoryList;

class ExportLifetimeCsv extends \Magento\Reports\Controller\Adminhtml\Report\Sales {
	
    /**
     * @var \Magento\Framework\Stdlib\DateTime\Filter\DateTime
     */
    protected $_dateTimeFilter;
    
    /**
     * @param \Magento\Backend\App\Action\Context $context
     * @param \Magento\Framework\App\Response\Http\FileFactory $fileFactory
     * @param \Magento\Framework\Stdlib\DateTime\Filter\Date $dateFilter
     * @param \Magento\Framework\Stdlib\DateTime\TimezoneInterface $timezone
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Framework\App\Response\Http\FileFactory $fileFactory,
        \Magento\Framework\Stdlib\DateTime\Filter\Date $dateFilter,
        \Magento\Framework\Stdlib\DateTime\Filter\DateTime $dateTimeFilter,
        \Magento\Framework\Stdlib\DateTime\TimezoneInterface $timezone
    ) {
        parent::__construct($context, $fileFactory, $dateFilter, $timezone);
		$this->_dateTimeFilter = $dateTimeFilter;
    }
    
    /**
     * Report action init operations
     *
     * @param array|\Magento\Framework\DataObject $blocks
     * @return $this
     */
    public function _initReportAction($blocks) {
        if (!is_array($blocks)) {
            $blocks = [$blocks];
        }

        $requestData = $this->_objectManager->get(
            'Magento\Backend\Helper\Data'
        )->prepareFilterString(
            $this->getRequest()->getParam('filter')
        );
        $inputFilter = new \Zend_Filter_Input(
            ['from' => $this->_dateTimeFilter, 'to' => $this->_dateTimeFilter],
            [],
            $requestData
        );
        $requestData = $inputFilter->getUnescaped();
        $requestData['store_ids'] = $this->getRequest()->getParam('store_ids');
        $params = new \Magento\Framework\DataObject();
		
        foreach ($requestData as $key => $value) {
            if (!empty($value)) {
                $params->setData($key, $value);
            }
        }
        
        foreach ($blocks as $block) {
            if ($block) {
                #$block->setPeriodType($params->getData('period_type'));
                $block->setFilterData($params);
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
        $fileName = 'toppikreport_subscription_lifetime_report.csv';
        $grid = $this->_view->getLayout()->createBlock('Toppik\Report\Block\Adminhtml\Subscription\Lifetime\Grid');
        $this->_initReportAction($grid);
        return $this->_fileFactory->create($fileName, $grid->getCsvFile(), DirectoryList::VAR_DIR);
    }
	
}
