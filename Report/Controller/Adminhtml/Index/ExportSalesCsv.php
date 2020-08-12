<?php
namespace Toppik\Report\Controller\Adminhtml\Index;

use Magento\Framework\App\ResponseInterface;
use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\Stdlib\DateTime\TimezoneInterface;

class ExportSalesCsv extends \Magento\Reports\Controller\Adminhtml\Report\Sales
{
	
    /**
     * @var \Magento\Framework\Stdlib\DateTime\Filter\DateTime
     */
    protected $_dateTimeFilter;

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
        \Magento\Framework\Stdlib\DateTime\Filter\DateTime $dateTimeFilter,
        TimezoneInterface $timezone
    ) {
        parent::__construct($context,$fileFactory,$dateFilter,$timezone);
		$this->_dateTimeFilter = $dateTimeFilter;
    }
	
    /**
     * Report action init operations
     *
     * @param array|\Magento\Framework\DataObject $blocks
     * @return $this
     */
    public function _initReportAction($blocks)
    {
        if (!is_array($blocks)) {
            $blocks = [$blocks];
        }
        
        $this->getRequest()->setParam('filter', trim(rawurldecode(rawurldecode($this->getRequest()->getParam('filter')))));
        
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

		if($params->getData('report_type') == 'order_number' && isset($requestData['number_from']) && isset($requestData['number_to'])) {
			$params->setFrom((int) $requestData['number_from']);
			$params->setTo((int) $requestData['number_to']);
		}
		
        foreach ($blocks as $block) {
            if ($block) {
                $block->setPeriodType($params->getData('period_type'));
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
    public function execute()
    {
        $fileName = 'sales.csv';
        $grid = $this->_view->getLayout()->createBlock('Toppik\Report\Block\Adminhtml\Report\Grid');
        $this->_initReportAction($grid);
        return $this->_fileFactory->create($fileName, $grid->getCsvFile(), DirectoryList::VAR_DIR);
    }
	
}
