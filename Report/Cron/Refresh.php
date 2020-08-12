<?php
namespace Toppik\Report\Cron;

class Refresh {
	
    /**
     * @var ObjectManagerInterface
     */
    private $objectManager;
	
    /**
     * @var \Magento\Framework\Event\ManagerInterface
     */
    private $eventManager;
	
    /**
     * Refresh constructor.
     * @param \Magento\Framework\Event\ManagerInterface $eventManager
     */
    public function __construct(
        \Magento\Framework\ObjectManagerInterface $objectManager,
        \Magento\Framework\Event\ManagerInterface $eventManager
    ) {
        $this->objectManager = $objectManager;
        $this->eventManager = $eventManager;
    }
	
    public function execute() {
		$this->objectManager->get('\Toppik\Report\Model\Subscription\Monthly')->refresh();
        $this->objectManager->get('\Toppik\Report\Model\Subscription\Future')->refresh();
        $this->objectManager->get('\Toppik\Report\Model\Subscription\MonthlySum')->refresh();
        $this->objectManager->get('\Toppik\Report\Model\Subscription\Pivot')->refresh();
    }
	
}
