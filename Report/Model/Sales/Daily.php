<?php
namespace Toppik\Report\Model\Sales;

use Magento\Framework\Model\AbstractModel;


class Daily  extends AbstractModel
{

    /**
     * Prefix of model events names
     *
     * @var string
     */
    protected $_eventPrefix = 'toppikreport_dailysales';

    /**
     * Parameter name in event
     *
     * In observe method you can use $observer->getEvent()->getObject() in this case
     *
     * @var string
     */
    protected $_eventObject = 'dailysales';

    /**
     * @param \Magento\Framework\Model\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\Framework\Model\ResourceModel\AbstractResource $resource
     * @param \Magento\Framework\Data\Collection\AbstractDb $resourceCollection
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\Model\Context $context,
        \Magento\Framework\Registry $registry,
       // \Magento\Framework\Model\ResourceModel\AbstractResource $resource = null,
        \Toppik\Report\Model\ResourceModel\Report\Sales\Daily\Collection $resourceCollection,
        array $data = []
    ){

        parent::__construct($context, $registry, null, $resourceCollection, $data);
    }
}