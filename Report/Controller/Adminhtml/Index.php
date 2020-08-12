<?php

namespace Toppik\Report\Controller\Adminhtml;

use Magento\Framework\Stdlib\DateTime\TimezoneInterface;

abstract class Index extends \Magento\Reports\Controller\Adminhtml\Report\AbstractReport
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
			$params->setFrom($requestData['number_from']);
			$params->setTo($requestData['number_to']);
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
     * Add report breadcrumbs
     *
     * @return $this
     */
    public function _initAction()
    {
        parent::_initAction();
        $this->_addBreadcrumb(__('Toppik Report'), __('Toppik Report'));
        return $this;
    }

    /**
     * Determine if action is allowed for reports module
     *
     * @return bool
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Toppik_Report::toppik');
    }
}
