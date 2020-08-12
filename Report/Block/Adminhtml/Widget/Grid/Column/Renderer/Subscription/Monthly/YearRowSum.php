<?php
namespace Toppik\Report\Block\Adminhtml\Widget\Grid\Column\Renderer\Subscription\Monthly;

class YearRowSum extends \Magento\Backend\Block\Widget\Grid\Column\Renderer\Currency {
    
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
