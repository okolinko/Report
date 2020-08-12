<?php
namespace Toppik\Report\Processor;

use Magento\Framework\App\Filesystem\DirectoryList;

class DailySales
{
	
    /**
     * @var \Magento\Framework\ObjectManagerInterface
     */
    private $objectManager;
	
    /**
     * @var \Magento\Framework\Stdlib\DateTime\DateTime
     */
    private $dateTime;
	
    /**
     * @var \Toppik\Report\Model\Sales\Daily
     */
    private $model;
	
    /**
     * @var \Toppik\Report\Helper\Report
     */
    private $reportHelper;

    private $csvHeaders = [
        'date_range',
        'source',
        'merchant_source',
        'grand_total',
        'subtotal' ,
        'shipping',
        'taxes',
        'discount',
        'refund',
        'order_volume',
        'total_unit_sales'
    ];


    /**
     * @var \Magento\Framework\App\ViewInterface
     */
    protected $view;

    /**
     * @var \Magento\Framework\App\Filesystem\DirectoryList
     */
    protected $directory_list;

    /**
     * @var \Magento\Framework\Filesystem
     */
    protected $filesystem;

    /**
     * @var array
     */
    protected $attachments = [];


    /**
     * ActiveProfiles constructor.
     * @param \Magento\Framework\ObjectManagerInterface $objectManager
     * @param \Magento\Framework\Stdlib\DateTime\DateTime $dateTime
     */
    public function __construct(
        \Magento\Framework\ObjectManagerInterface $objectManager,
        \Magento\Framework\Stdlib\DateTime\DateTime $dateTime,
        \Toppik\Report\Model\Sales\Daily $model,
        \Magento\Backend\App\Action\Context $context,
        DirectoryList $directory_list,
        \Magento\Framework\Filesystem $filesystem,
		\Toppik\Report\Helper\Report $reportHelper
    ) {
        $this->objectManager = $objectManager;
        $this->dateTime = $dateTime;
		$this->model = $model;
		$this->reportHelper = $reportHelper;
        $this->view = $context->getView();
        $this->directory_list = $directory_list;
        $this->filesystem = $filesystem;
    }
	
    public function execute() {
		try {
			$this->reportHelper->log($this->model->getSubject() . ' - > start', []);

            $collection = $this->model->getResourceCollection();

            $customerGroups = ((int)$this->model->getCustomerGroupsSourceId() > 1) ? $this->model->getCustomerGroupsSourceId() : 1;

            $collection
                ->setDateRange($this->model->getDateRangeFrom(),$this->model->getDateRangeTo())
                ->setCustomerGroups($customerGroups)
                ->setGrouping($this->model->getGrouping());
			
			$this->reportHelper->log(sprintf('Found %s item(s)', count($collection)), []);
			
			if(count($collection)) {
				$this->reportHelper->send(
					$collection,
                    $this->model->getMessage(),
					#['Date Range', 'Order Source', 'Merchant Source', 'Grand Total', 'Subotal', 'Shipping', 'Taxes', 'Discount', 'Refund', 'Order Volume', 'Total Unit Sales'],
                    $this->getCsvHeaders(),
                    $this->model->getSubject(),
                    $this->reportHelper->getDailySalesEmails()
				);
				

			}
			
			$this->reportHelper->log(sprintf('%s %s -> end', str_repeat('-', 10),$this->model->getSubject()));
		} catch(\Exception $e) {
			$this->reportHelper->log(sprintf('%s Error during processing report DailySales: %s', str_repeat('=', 5), $e->getMessage()), [], \Toppik\Report\Logger\Logger::ERROR);
		}
    }

    public function generateCsvFromGrid()
    {

        $fileName = false;

        try {
            $this->reportHelper->log($this->model->getSubject() . ' - > start generating file from grid', []);


            $customerGroups = ((int)$this->model->getCustomerGroupsSourceId() > 1) ? $this->model->getCustomerGroupsSourceId() : 1;

            $grid = $this->view->getLayout()->createBlock('Toppik\Report\Block\Adminhtml\Sales\Daily\Grid');

            $params = new \Magento\Framework\DataObject();

            $grid->setData([]); #reset

            $params->setData($this->model->getData())
                ->setFrom($this->model->getDateRangeFrom())
                ->setTo($this->model->getDateRangeTo())
                ->setGrouping($this->model->getGrouping())
                ->setCustomerGroup($customerGroups);
            $grid->setFilterData($params);

            $file = $grid->getCsvFile();

            $fileName = $file['value'];

            $this->reportHelper->log(sprintf('%s -> file generated successfully', str_repeat('-', 10)));
        } catch(\Exception $e) {
            $this->reportHelper->log(sprintf('%s Error during processing report DailySales: %s', str_repeat('=', 5), $e->getMessage()), [], \Toppik\Report\Logger\Logger::ERROR);
        }

        return $this->directory_list->getPath('var') . '/' .  $fileName;
    }




    public function createFileForAttacment()
    {
        $file = $this->generateCsvFromGrid();

        if($file) {
            $this->attachments[$this->getFileName()] = $file;
        }

        return $this;

    }

    public function executeMultiple()
    {

        try {
            $this->reportHelper->log($this->model->getSubject() . ' - > start', []);

            $this->reportHelper->sendMiltipleFiles(
                $this->attachments,
                $this->model->getMessage(),
                $this->model->getSubject(),
                $this->reportHelper->getDailySalesEmails()
            );

            $this->reportHelper->log(sprintf('%s %s -> end', str_repeat('-', 10),$this->model->getSubject()));

            $this->resetAttachments();

        } catch(\Exception $e) {
            $this->reportHelper->log(sprintf('%s Error during processing report DailySales: %s', str_repeat('=', 5), $e->getMessage()), [], \Toppik\Report\Logger\Logger::ERROR);
        }

        return $this;

    }


    /**
     * @param $id
     * @return $this
     */
    public function setCustomerGroupsSourceId($id)
    {
        $this->model->setCustomerGroupsSourceId($id);

        return $this;
    }

    public function setGrouping($grouping)
    {
        $this->model->setGrouping($grouping);
        return $this;
    }

    public function setMessage($message)
    {
        $this->model->setMessage($message);
        return $this;
    }

    public function setSubject($subject)
    {
        $this->model->setSubject($subject);
        return $this;
    }

    public function setDailyDateRange()
    {
        $date = new \DateTime();

        $this->model->setDateRangeFrom($date->format('Y-m-d 00:00:00'));
        $this->model->setDateRangeTo($date->format('Y-m-d 23:59:59'));

        return $this;

    }

    public function setMonthlyDateRange()
    {

        $from =  new \DateTime();
        $from->modify('first day of this month');
        // $from->sub(new \DateInterval("P1D"));
        
        $to = new \DateTime();

        $this->model->setDateRangeFrom($from->format('Y-m-d 00:00:00'));
        $this->model->setDateRangeTo($to->format('Y-m-d 23:59:59'));

        return $this;

    }

    public function setQuarterlyDateRange()
    {

        $from =  new \DateTime();

        $startMonth = ceil((int)$from->format('n')/3)*3-2;

        $from->setDate(date('Y'), $startMonth, 1);
        // $from->sub(new \DateInterval("P1D"));
        
        $to = new \DateTime();

        $this->model->setDateRangeFrom($from->format('Y-m-d 00:00:00'));
        $this->model->setDateRangeTo($to->format('Y-m-d 23:59:59'));

        return $this;

    }

    public function setYearlyDateRange()
    {

        $from =  new \DateTime();
        $from->setDate(date('Y'), 1, 1);
        // $from->sub(new \DateInterval("P1D"));
        
        $to = new \DateTime();

        $this->model->setDateRangeFrom($from->format('Y-m-d 00:00:00'));
        $this->model->setDateRangeTo($to->format('Y-m-d 23:59:59'));

        return $this;

    }

    /**
     * @return string
     */
    public function getFileName()
    {
        $groupingPart = '';

        if($this->model->getGrouping()) {
            $groupingPart = $this->model->getGrouping() . '_';
        }

        return 'toppik_daily_sales_report_' . $groupingPart . $this->dateTime->gmtDate('Y-m-d') . '.csv';
    }

    /**
     * @return \Toppik\Report\Helper\Report
     */
    public function getHelper()
    {
        return $this->reportHelper;
    }


    /**
     * @return array
     */
    protected function getCsvHeaders()
    {
        return $this->csvHeaders;
    }


    protected function resetAttachments()
    {

        $dir = $this->filesystem->getDirectoryWrite(DirectoryList::VAR_DIR);

        foreach ($this->attachments as $file) {
            $dir->delete( 'export/' . basename($file)  );
        }

        return $this;
    }

}
