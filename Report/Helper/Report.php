<?php
namespace Toppik\Report\Helper;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Toppik\Report\Logger\FileHandlerFactory;
use Toppik\Report\Logger\LoggerFactory;
use Toppik\Report\Logger\Logger;

class Report {
	
    /**
     * @var ScopeConfigInterface
     */
    private $scopeConfig;
	
    /**
     * @var Logger
     */
    private $logger;
	
    /**
     * @var DateTime
     */
    protected $dateTime;
	
    /**
     * @var TransportBuilder
     */
    protected $transportBuilder;
	
    /**
     * ExportOrder constructor.
     * @param ScopeConfigInterface $scopeConfig
     * @param LoggerFactory $loggerFactory
     * @param FileHandlerFactory $fileHandlerFactory
     */
    public function __construct(
        ScopeConfigInterface $scopeConfig,
        LoggerFactory $loggerFactory,
        FileHandlerFactory $fileHandlerFactory,
        \Magento\Framework\Stdlib\DateTime\DateTime $dateTime,
        \Toppik\Report\Model\TransportBuilder $transportBuilder
    ) {
        $this->scopeConfig = $scopeConfig;
        $this->logger = $loggerFactory->create();
        $this->dateTime = $dateTime;
        $this->transportBuilder = $transportBuilder;
		
        /* @var FileHandler $fileHandler */
        $fileHandler = $fileHandlerFactory->create([
            'filename' => '/var/log/' . $this->getLogFile(),
        ]);
		
        $this->logger->pushHandler($fileHandler);
    }
	
    /**
     * @return string[]
     */
    public function getEmails() {
        return explode(',', (string) $this->scopeConfig->getValue('toppikreport_settings/general_options/notification_emails'));
    }

    public function getDailySalesEmails() {
        return explode(',', (string) $this->scopeConfig->getValue('toppikreport_settings/general_options/daily_sales_emails'));
    }

    public function isDailyEmailEnabled() {
        return (bool)$this->scopeConfig->getValue('toppikreport_settings/general_options/daily_sales_send_daily_email');
    }


    /**
     * @return bool
     */
    public function isLogEnabled() {
        return !! $this->scopeConfig->getValue('toppikreport_settings/general_options/log');
    }
	
    /**
     * @return string
     */
    public function getLogFile() {
        return (string) $this->scopeConfig->getValue('toppikreport_settings/general_options/log_file');
    }
	
    /**
     * @param string $message
     * @param array $context
     * @param int $level
     */
    public function log($message, array $context = [], $level = Logger::DEBUG) {
        if($this->isLogEnabled()) {
            $this->logger->addRecord($level, $message, $context);
        }
    }

    public function send($data, $message, $headers, $subject = 'Toppik system integrations status report', $emails = []) {
        $rows 		= $this->_generateRows($data, $headers);
        $csv 		= $this->_generateFile($rows, $headers);
        $emails     = (count($emails) < 1) ? $this->getEmails() : $emails;


        $this->transportBuilder->reset();

        $this->transportBuilder
            ->setTemplateIdentifier('toppikreport_report')
            ->setTemplateOptions([
                'area' 	=> 'frontend',
                'store' => 0,
            ])
            ->setTemplateVars([
                'now' 		=> $this->dateTime->gmtDate('Y-m-d H:i:s'),
                'subject' 	=> $subject,
                'message' 	=> $message
            ])
            ->attachFile(
                'toppik_system_integrations_report_' . $this->dateTime->gmtDate('Y-m-d') . '.csv',
                $csv
            )
            ->setFrom([
                'email' => 'notification@toppik.com',
                'name' 	=> 'Toppik System',
            ])
            ->addTo($emails, 'Toppik System Support')
            ->getTransport()
            ->sendMessage();

        $this->transportBuilder->reset();

        $this->log('Sent email with report');
    }

    public function sendMiltipleFiles($files = [], $message, $subject = 'Toppik system integrations status report', $emails = []) {
        $emails     = (count($emails) < 1) ? $this->getEmails() : $emails;


        $this->transportBuilder->reset();

        $this->transportBuilder
            ->setTemplateIdentifier('toppikreport_report')
            ->setTemplateOptions([
                'area' 	=> 'frontend',
                'store' => 0,
            ])
            ->setTemplateVars([
                'now' 		=> $this->dateTime->gmtDate('Y-m-d H:i:s'),
                'subject' 	=> $subject,
                'message' 	=> $message
            ])
            ->setFrom([
                'email' => 'notification@toppik.com',
                'name' 	=> 'Toppik System',
            ])
            ->addTo($emails, 'Toppik System Support');

        foreach($files as $fileName=>$filePath) {

            if(file_exists($filePath)) {
                $this->transportBuilder
                    ->attachFile(
                        $fileName,
                        file_get_contents($filePath)
                    );
            }
        }


        $this->transportBuilder
            ->getTransport()
            ->sendMessage();

        $this->transportBuilder->reset();

        $this->log('Sent email with report');
    }

    protected function _generateRows($collection, $headers) {
		$rows = array();
		
        foreach($collection as $_item) {
			$values = array();
			
			foreach($headers as $_header) {
				$values[$_header] = isset($_item[$_header]) ? $_item[$_header] : '';
			}
			
			$rows[] = $values;
        }
		
		return $rows;
	}
	
    protected function _generateFile($rows, $headers) {
        $fd = fopen('php://temp/maxmemory:'.(1024 * 1024 * 10)/*10MB*/, 'w');
		
        fputcsv($fd, $headers);
		
        foreach($rows as $row) {
			$values = array();
			
			foreach($headers as $_header) {
				$values[] = isset($row[$_header]) ? $row[$_header] : '';
			}
			
            fputcsv($fd, $values);
        }
		
        rewind($fd);
		
        $csv = stream_get_contents($fd);
		
        fclose($fd);
		
        return $csv;
    }
	
}
