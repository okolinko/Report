<?php
namespace Toppik\Report\Model\Sales\Source;


use Magento\Framework\Data\OptionSourceInterface;


class CustomerGroups implements OptionSourceInterface
{

    const REGULAR_CUSTOMERS = [0,1,4];
    const PRO_ORDERS = [2,3,5];

    protected $_customerGroups = [
        1 => self::REGULAR_CUSTOMERS,
        2 => self::PRO_ORDERS
    ];

    /**
     * Return array of options as value-label pairs
     *
     * @return array Format: array(array('value' => '<value>', 'label' => '<label>'), ...)
     */
    public function toOptionArray()
    {
        $options[] = ['label' => 'Regular Customers', 'value' => 1];
        $options[] = ['label' => 'Pro orders', 'value' => 2];

        return $options;
    }

    /**
     * @param $id
     * @return null
     */
    public function getCustomeGroupById($id)
    {
        $result = null;

        if(isset($this->_customerGroups[(int)$id])) {
            $result = $this->_customerGroups[(int)$id];
        }

        return $result;
    }
}