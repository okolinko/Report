<?php
namespace Toppik\Report\Block\Adminhtml\System\Integrations\Entity\Edit;

class Form extends \Magento\Backend\Block\Widget\Form\Generic {
    
	/**
	 * @var \Magento\User\Model\User
	 */
	protected $_adminUserModel;
    
    /**
     * Form constructor.
     * @param \Magento\Store\Model\System\Store $systemStore
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Framework\Registry $coreRegistry
     * @param \Magento\Framework\Data\FormFactory $formFactory
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Framework\Registry $coreRegistry,
        \Magento\Framework\Data\FormFactory $formFactory,
		\Magento\User\Model\User $adminUserModel,
        array $data
    ) {
    	$this->_adminUserModel = $adminUserModel;
        parent::__construct($context, $coreRegistry, $formFactory, $data);
    }
    
    protected function _construct() {
        parent::_construct();
        $this->setId('entity_type_form');
        $this->setTitle(__('Item Information'));
    }
    
    /**
     * @return $this
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    protected function _prepareForm() {
        $options                = [];
        $model                  = $this->_coreRegistry->registry('entity_type_item');
        $adminUserCollection    = $this->_adminUserModel->getCollection()->setOrder('main_table.username', 'ASC');
        
        foreach($adminUserCollection as $customer) {
            $options[] = [
                            'value' => $customer->getId(),
                            'label' => sprintf('%s (%s %s)', $customer->getUsername(), $customer->getFirstname(), $customer->getLastname())
                        ];
        }
        
        /* @var FormClass $form */
        $form = $this->_formFactory->create([
            'data' => ['id' => 'edit_form', 'action' => $this->getData('action'), 'method' => 'post']
        ]);
        
        $form->setHtmlIdPrefix('');
        
        $generalFieldset = $form->addFieldset(
            'general_fieldset',
            ['legend' => __('General Information'), 'class' => 'fieldset-wide']
        );
        
        $generalFieldset->addField('id', 'hidden', ['name' => 'id']);
        
        $generalFieldset->addField(
            'entity_type_code',
            'text',
            [
                'label'     => __('Entity Type Code'),
                'title'     => __('Entity Type Code'),
                'name'      => 'entity_type_code',
                'required'  => true
            ]
        );
        
        $generalFieldset->addField(
            'entity_type_label',
            'text',
            [
                'label'     => __('Entity Type Label'),
                'title'     => __('Entity Type Label'),
                'name'      => 'entity_type_label',
                'required'  => true
            ]
        );
        
        $generalFieldset->addField(
            'entity_type_description',
            'textarea',
            [
                'label'     => __('Entity Type Description'),
                'title'     => __('Entity Type Description'),
                'name'      => 'entity_type_description',
                'required'  => false
            ]
        );
        
        $generalFieldset->addField(
            'admin_ids',
            'multiselect',
            [
                'label'     => __('Admin IDs'),
                'title'     => __('Admin IDs'),
                'name'      => 'admin_ids',
                'required'  => false,
                'values'    => $options
            ]
        );
        
        $form->setValues($model->getData());
        $form->setUseContainer(true);
        $this->setForm($form);
        
        return parent::_prepareForm();
    }
    
}
