<?php
namespace Toppik\Report\Block\Adminhtml\Widget\Grid\Column\Renderer\Subscription\Monthly;

class YearRow extends \Magento\Backend\Block\Widget\Grid\Column\Renderer\Text {
    
    /**
     * Renders column
     *
     * @param \Magento\Framework\DataObject $row
     * @return string
     */
    public function render(\Magento\Framework\DataObject $row) {
        if(strlen($row->getData('period')) === 4) {
            return '';
        }
        
        return parent::render($row);
    }
    
}
