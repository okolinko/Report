<?php
namespace Toppik\Report\Processor;

class DailySystem {
	
    /**
     * @var \Magento\Framework\ObjectManagerInterface
     */
    private $objectManager;
	
    /**
     * @var \Magento\Framework\Stdlib\DateTime\DateTime
     */
    private $dateTime;
	
    /**
     * @var \Toppik\Report\Model\Daily\System
     */
    private $model;
	
    /**
     * @var \Toppik\Report\Helper\Report
     */
    private $reportHelper;
	
    /**
     * ActiveProfiles constructor.
     * @param \Magento\Framework\ObjectManagerInterface $objectManager
     * @param \Magento\Framework\Stdlib\DateTime\DateTime $dateTime
     */
    public function __construct(
        \Magento\Framework\ObjectManagerInterface $objectManager,
        \Magento\Framework\Stdlib\DateTime\DateTime $dateTime,
		\Toppik\Report\Model\Daily\System $model,
		\Toppik\Report\Helper\Report $reportHelper
    ) {
        $this->objectManager = $objectManager;
        $this->dateTime = $dateTime;
		$this->model = $model;
		$this->reportHelper = $reportHelper;
    }
	
    public function execute() {
		try {
			$this->reportHelper->log('DailySystem - > start', []);
			
			$collection = $this->model->getPendingQueue();
			
			$this->reportHelper->log(sprintf('Found %s item(s)', count($collection)), []);
			
			if(count($collection)) {
				$this->reportHelper->send(
					$collection,
					'Toppik system integrations status report',
					array('Entity Type', 'Entity ID', 'Date', 'Message', 'Amount', 'Customer ID', 'Customer Name', 'Customer Email', 'Customer Phone')
				);
				
				$this->model->updateById(array_keys($collection));
			}
			
			$this->reportHelper->log(sprintf('%s DailySystem -> end', str_repeat('-', 10)));
		} catch(\Exception $e) {
			$this->reportHelper->log(sprintf('%s Error during processing report daily: %s', str_repeat('=', 5), $e->getMessage()), [], \Toppik\Report\Logger\Logger::ERROR);
		}
    }
	
}
