<?php
namespace Toppik\Report\Observer;

class DailySalesRegularCustomers implements \Magento\Framework\Event\ObserverInterface {
	
    /**
     * @var \Toppik\Report\Processor\DailySystem
     */
    private $processor;
	
    /**
     * DailySystem constructor.
     * @param \Toppik\Report\Processor\DailySales $processor
     */
    public function __construct(
		\Toppik\Report\Processor\DailySales $processor
    ) {
        $this->processor = $processor;
    }
	
    /**
     * @param \Magento\Framework\Event\Observer $observer
     * @return void
     */
    public function execute(\Magento\Framework\Event\Observer $observer) {


        /**
         * execute only if daily_sales_send_daily_email 1
         */
        if($this->processor->getHelper()->isDailyEmailEnabled() === false ) {
            return true;
        }

        $this->processor
            ->setCustomerGroupsSourceId(1)
            ->setMessage('Regular customers: Daily Sales Report')
            ->setSubject('Regular customers: Daily Sales Report');

        $this->processor
            ->setDailyDateRange()
            ->setGrouping('daily')
            ->createFileForAttacment();


        $this->processor
            ->setMonthlyDateRange()
            ->setGrouping('monthly')
            ->createFileForAttacment();

        $this->processor
            ->setQuarterlyDateRange()
            ->setGrouping('quarterly')
            ->createFileForAttacment();




        $this->processor
            ->setYearlyDateRange()
            ->setGrouping('yearly')
            ->createFileForAttacment();


        $this->processor->executeMultiple();


    }

}
