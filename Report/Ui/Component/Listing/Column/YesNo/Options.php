<?php
namespace Toppik\Report\Ui\Component\Listing\Column\YesNo;

class Options implements \Magento\Framework\Data\OptionSourceInterface {
    
    /**
     * @var array
     */
    protected $options;
    
    /**
     * Get options
     *
     * @return array
     */
    public function toOptionArray() {
        if($this->options === null) {
            $this->options = [
                [
                    'value' => 1,
                    'label' => __('Yes'),
                ],
                [
                    'value' => 0,
                    'label' => __('No'),
                ]
            ];
        }
        
        return $this->options;
    }
    
}
