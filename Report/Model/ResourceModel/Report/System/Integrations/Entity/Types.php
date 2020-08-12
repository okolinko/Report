<?php
namespace Toppik\Report\Model\ResourceModel\Report\System\Integrations\Entity;

class Types extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb {
	
	const MAIN_TABLE = 'toppikreport_daily_system_entity_type';
	
    /**
     * Resource initialization
     *
     * @return void
     */
    protected function _construct() {
        $this->_init(self::MAIN_TABLE, 'id');
    }
	
}
