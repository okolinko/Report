<?php

/**
 * Class ShipMethod
 */

namespace Toppik\Report\Block\Adminhtml\Report\Column\Renderer;

use Magento\Framework\DataObject;
use \Toppik\Edge\Model\ExportOrder\Xml;

class ShipMethod extends \Magento\Backend\Block\Widget\Grid\Column\Renderer\AbstractRenderer
{
    protected $_shippingMethod_dict = [];

    /**
     * @param \Magento\Backend\Block\Context $context
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Context $context,
        Xml $xml,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->_shippingMethod_dict = $xml->getShippingMethodDict();
    }

    /**
     * @param Object $row
     * @return mixed
     */
    protected function _getValue(DataObject $row)
    {
        $shippingMethodCode = $row->getData($this->getColumn()->getIndex());

        return $this->formatShippingMethodCode($shippingMethodCode);
    }

    protected function formatShippingMethodCode($shippingMethodCode)
    {

        $formatShippingMethodCode =  $this->convertMatrixrateCodes($shippingMethodCode);


        if($formatShippingMethodCode  === false) {
            $formatShippingMethodCode = $shippingMethodCode;
        }

        return $formatShippingMethodCode;
    }

    protected function convertMatrixrateCodes($shippingMethodCode)
    {
        $formatShippingMethodCode = false;

        if(preg_match('#^matrixrate_(.+)_(\d+)$#', $shippingMethodCode, $m)) {
            $formatShippingMethodCode = array_search($m[1],$this->_shippingMethod_dict);
        }

        //convert remaining codes to standart
        if($formatShippingMethodCode === false && preg_match('#^matrixrate_LCR_(\d+)$#', $shippingMethodCode)) {
            $formatShippingMethodCode = 'productmatrix_Standard';
        }

        return $formatShippingMethodCode;
    }

}