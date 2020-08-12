<?php
namespace Toppik\Report\Controller\Debug;

class ImportCustomerData extends \Magento\Framework\App\Action\Action {
	
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
            $attribute_code = 'rep_code';
            
            while(false !== ($csvLine = $stream->readCsv())) {
                if(empty($csvLine)) {
                    continue;
                }
                
                $id     = array_shift($csvLine);
                $value  = array_shift($csvLine);
                
                if((int) $id > 0) {
                    $data[$id] = empty($value) ? 'NOREPCODE' : $value;
                }
            }
            
            $stream->close();
            
            if(count($data) > 0) {
                $attribute = $this->_objectManager->create('Magento\Customer\Model\Customer')->getResource()->getAttribute($attribute_code);
                
                if(!$attribute) {
                    throw new \Exception(sprintf('Attribute with code %s does not exist', $attribute_code));
                }
                
                if(!$attribute->usesSource()) {
                    throw new \Exception(sprintf('Attribute with code %s does not have options', $attribute_code));
                }
                
                $options = array();
                
                foreach($attribute->getSource()->getAllOptions() as $_option) {
                    if(isset($_option['label']) && isset($_option['value']) && !empty($_option['label']) && !empty($_option['value'])) {
                        $options[$_option['label']] = $_option['value'];
                    }
                }
                
                foreach($data as $_id => $_value) {
                    try {
                        if((int) $_id > 0 && !empty($_value)) {
                            $customer = $this->_objectManager->create('Magento\Customer\Model\Customer')->load($_id);
                            
                            if(!$customer->getId()) {
                                throw new \Exception(sprintf('Customer with ID %s does not exist', $_id));
                            }
                            
                            if(!isset($options[$_value])) {
                                throw new \Exception(sprintf('Customer with ID %s does not have appropriate value %s', $_id, $_value));
                            }
                            
                            echo __('Before set data for customer ID %1, %2 = %3<br />', $customer->getId(), $attribute_code, $customer->getData($attribute_code));
                            
                            $customer->setData($attribute_code, $options[$_value]);
                            
                            echo __('Updating customer ID %1, %2 = %3<br />', $customer->getId(), $attribute_code, $options[$_value]);
                            echo __('Updated customer ID %1, %2 = %3<br />', $customer->getId(), $attribute_code, $customer->getData($attribute_code));
                            
                            $customer->getResource()->saveAttribute($customer, $attribute_code);
                            
                            $this->_resource->getConnection(\Magento\Framework\App\ResourceConnection::DEFAULT_CONNECTION)->query(
                                sprintf(
                                    'UPDATE %s SET %s = "%s" WHERE customer_id = %s',
                                    $this->_resource->getTableName('sales_order'),
                                    $attribute_code,
                                    $_value,
                                    $customer->getId()
                                )
                            );
                            
                            file_put_contents(BP . '/var/log/customers-update.txt', $customer->getId() . "\n", FILE_APPEND | LOCK_EX);
                            
                            echo __('---------- Updated customer ID %1<br />', $customer->getId());
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
