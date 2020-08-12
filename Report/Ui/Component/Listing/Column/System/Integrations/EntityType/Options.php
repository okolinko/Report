<?php
namespace Toppik\Report\Ui\Component\Listing\Column\System\Integrations\EntityType;

class Options implements \Magento\Framework\Data\OptionSourceInterface {
	
    /**
     * @var array
     */
    protected $options;
	
    /**
     * @var \Toppik\Report\Processor\ProcessDrtvCs
     */
    private $model;
	
    /**
     * ProcessProfiles constructor.
     * @param \Toppik\Report\Processor\ProcessDrtvCs $model
     */
    public function __construct(
		\Toppik\Report\Model\System\Integrations\Entity\Types $model
    ) {
        $this->model = $model;
    }
	
    /**
     * Get options
     *
     * @return array
     */
    public function toOptionArray() {
        if($this->options === null) {
            $this->options = $this->model->toOptionArray();
        }
		
        return $this->options;
    }
	
}
