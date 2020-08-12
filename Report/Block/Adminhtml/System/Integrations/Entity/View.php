<?php
namespace Toppik\Report\Block\Adminhtml\System\Integrations\Entity;

class View extends \Magento\Backend\Block\Widget\Form\Container {
    
    protected $_coreRegistry;
    
    /**
     * Edit constructor.
     * @param Context $context
     * @param Registry $registry
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Widget\Context $context,
        \Magento\Framework\Registry $coreRegistry,
        array $data
    ) {
        $this->_coreRegistry = $coreRegistry;
        parent::__construct($context, $data);
    }
    
    protected function _construct() {
        $this->_blockGroup  = 'Toppik_Report';
        $this->_controller  = 'adminhtml_system_integrations_entity';
        $this->_headerText  = __('System Integrations Entity Types');
        $this->_objectId    = 'entity_type';
        
        $model = $this->_coreRegistry->registry('entity_type_item');
        
        $this->buttonList->update('save', 'label', __('Save'));
        $this->buttonList->remove('add');
        
        parent::_construct();
    }
    
    /**
     * @return mixed
     */
    public function getHeaderText() {
        return __('System Integrations Entity Types');
    }
    
    public function getSaveUrl() {
		return $this->getUrl('toppikreport/system/integrations_entity_save');
    }
	
    public function getBackUrl() {
		return $this->getUrl('toppikreport/system/integrations_entity_types');
    }
	
}
