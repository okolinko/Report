<?php
namespace Toppik\Report\Block\Adminhtml\Widget\Grid\Column\Renderer\Subscription;

abstract class AbstractClass extends \Magento\Backend\Block\Widget\Grid\Column\Renderer\Text {
    
    protected $_coreRegistry;
	
    /**
     * @param \Magento\Backend\Block\Context $context
     * @param \Magento\Framework\Json\EncoderInterface $jsonEncoder
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Context $context,
        \Magento\Framework\Registry $coreRegistry,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->_coreRegistry = $coreRegistry;
    }
    
    /**
     * Renders column
     *
     * @param \Magento\Framework\DataObject $row
     * @return string
     */
    public function render(\Magento\Framework\DataObject $row) {
        $value = sprintf(
            '<a target="_blank" href="%s">%s</a>',
            $this->_getValueUrl($row),
            $this->_getValue($row)
        );
        
        return $value;
    }
    
    /**
     * Render column for export
     *
     * @param Object $row
     * @return string
     */
    public function renderExport(\Magento\Framework\DataObject $row) {
        return parent::render($row);
    }
    
}
