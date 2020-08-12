<?php
namespace Toppik\Report\Observer;

class DailySystem implements \Magento\Framework\Event\ObserverInterface {
	
    /**
     * @var \Toppik\Report\Processor\DailySystem
     */
    private $model;
	
    /**
     * DailySystem constructor.
     * @param \Toppik\Report\Processor\DailySystem $model
     */
    public function __construct(
		\Toppik\Report\Processor\DailySystem $model
    ) {
        $this->model = $model;
    }
	
    /**
     * @param Observer $observer
     * @return void
     */
    public function execute(\Magento\Framework\Event\Observer $observer) {
        $this->model->execute();
    }
	
}
