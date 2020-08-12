<?php
namespace Toppik\Report\Controller\Debug;

class FixCustomers extends \Magento\Framework\App\Action\Action {
	
    /**
     * @var \Magento\Framework\App\ResourceConnection
     */
    protected $_resource;
    
    /**
     * Filesystem instance
     *
     * @var \Magento\Framework\Filesystem
     */
    protected $_filesystem;
    
    /**
     * @var Magento\Framework\App\Filesystem\DirectoryList
     */
    private $_directoryList;
	
	public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Magento\Framework\App\ResourceConnection $resource,
        \Magento\Framework\Filesystem $filesystem,
        \Magento\Framework\App\Filesystem\DirectoryList $directoryList
	) {
        $this->_resource = $resource;
        $this->_filesystem = $filesystem;
        $this->_directoryList = $directoryList;
        parent::__construct($context);
	}
	
    public function execute() {
        try {
            $data           = array();
            $csvFile        = 'customers.csv';
            $tmpDirectory   = $this->_filesystem->getDirectoryRead(\Magento\Framework\App\Filesystem\DirectoryList::TMP);
            $stream         = $tmpDirectory->openFile($csvFile);
            
            while(false !== ($csvLine = $stream->readCsv())) {
                if(empty($csvLine)) {
                    continue;
                }
                
                $id     = array_shift($csvLine);
                $email  = array_shift($csvLine);
                
                if((int) $id > 0 && !empty($email)) {
                    $data[$id] = $email;
                }
            }
            
            $stream->close();
            
            if(count($data) > 0) {
                foreach($data as $_id => $_email) {
                    try {
                        if((int) $_id > 0 && !empty($_email)) {
                            $customer_id = (int) $this->_resource->getConnection(\Magento\Framework\App\ResourceConnection::DEFAULT_CONNECTION)->fetchOne(
                                sprintf(
                                    'SELECT entity_id FROM %s WHERE email = "%s"',
                                    $this->_resource->getTableName('customer_entity'),
                                    $_email
                                )
                            );
                            
                            if($customer_id && (int) $customer_id > 0) {
                                if((int) $customer_id === (int) $_id) {
                                    echo __('Customer with ID %1 and email %2 already exists with correct email<br />', $_id, $_email);
                                    continue;
                                }
                                
                                $_email = str_replace('@', '-exists@', $_email);
                                
                                $customer_id = (int) $this->_resource->getConnection(\Magento\Framework\App\ResourceConnection::DEFAULT_CONNECTION)->fetchOne(
                                    sprintf(
                                        'SELECT entity_id FROM %s WHERE email = "%s"',
                                        $this->_resource->getTableName('customer_entity'),
                                        $_email
                                    )
                                );
                            }
                            
                            if($customer_id && (int) $customer_id > 0) {
                                if((int) $customer_id === (int) $_id) {
                                    echo __('Customer with ID %1 and email %2 already exists with correct email<br />', $_id, $_email);
                                    continue;
                                }
                                
                                throw new \Exception(__('Customer with email %1 already exists - ID in file %2, ID in email %3', $_email, $_id, $customer_id));
                            }
                            
                            $sql = sprintf(
                                    'UPDATE %s SET email = "%s" WHERE entity_id = %s',
                                    $this->_resource->getTableName('customer_entity'),
                                    $_email,
                                    $_id
                                );
                            
                            file_put_contents(BP . '/var/log/customers.sql', $sql . ";\n", FILE_APPEND | LOCK_EX);
                            
                            $this->_resource->getConnection(\Magento\Framework\App\ResourceConnection::DEFAULT_CONNECTION)->query($sql);
                        }
                    } catch(\Exception $e) {
                        echo __('%1 Something went wrong while updating customer: %2<br />', str_repeat('=', 10), $e->getMessage());
                    }
                }
            }
        } catch(\Exception $e) {
            if(isset($stream)) {
                $stream->close();
            }
            
            echo __('%1 Something went wrong while importing data: %2<br />', str_repeat('=', 5), $e->getMessage());
        }
    }
    
}
