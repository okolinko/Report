<?php
namespace Toppik\Report\Cron;

class DailySales
{
	
    /**
     * @var \Magento\Framework\Event\ManagerInterface
     */
    private $eventManager;
	
    /**
     * DailySystem constructor.
     * @param \Magento\Framework\Event\ManagerInterface $eventManager
     */
    public function __construct(
        \Magento\Framework\Event\ManagerInterface $eventManager
    ) {
        $this->eventManager = $eventManager;
    }
	
    public function execute() {
        $result = new \Magento\Framework\DataObject;
        $this->eventManager->dispatch('toppikreport_daily_sales_regular_customers', ['result' => $result]);
        $this->eventManager->dispatch('toppikreport_daily_sales_pro_orders', ['result' => $result]);
    }
	
}
