<?php
namespace Toppik\Report\Model\System\Integrations\Entity;

class Types extends \Magento\Framework\Model\AbstractModel {
    
    /**
     * @var array
     */
    protected $_options;
    
    /**
     * Initialize resource model
     * @return void
     */
    protected function _construct() {
        $this->_init('\Toppik\Report\Model\ResourceModel\Report\System\Integrations\Entity\Types');
    }
    
    /**
     * Retrieve all options array
     *
     * @return array
     */
    public function getAllOptions() {
        if($this->_options === null) {
            $result = [];
            
            $connection = $this->_getResource()->getConnection();
            $table      = $this->_getResource()->getMainTable();
            $select     = $connection->select()->from($table, array('entity_type_code', 'entity_type_label'))->order('entity_type_code', 'ASC');
            $data       = $connection->fetchAll($select);
            
            if(is_array($data) && count($data) > 0) {
                foreach($data as $_option) {
                    $result[] = [
                                    'value' => $_option['entity_type_code'],
                                    'label' => $_option['entity_type_label']
                                ];
                }
            }
            
            $this->_options = $result;
        }
		
        return $this->_options;
    }
    
    public function toOptionArray() {
        $options = array();
        $options[] = ['label' => '', 'value' => ''];
        
        foreach($this->getAllOptions() as $option) {
            $options[] = ['label' => (string) $option['label'], 'value' => $option['value']];
        }
		
        return $options;
    }
    
}
