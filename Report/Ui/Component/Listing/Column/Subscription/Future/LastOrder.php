<?php
namespace Toppik\Report\Ui\Component\Listing\Column\Subscription\Future;

class LastOrder extends \Magento\Ui\Component\Listing\Columns\Column {
    
    /**
     * @var Url
     */
    private $url;
    
    public function __construct(
        \Magento\Backend\Model\Url $url,
        \Magento\Framework\View\Element\UiComponent\ContextInterface $context,
        \Magento\Framework\View\Element\UiComponentFactory $uiComponentFactory,
        array $components,
        array $data
    ) {
        parent::__construct($context, $uiComponentFactory, $components, $data);
        $this->url = $url;
    }
    
    /**
     * Prepare Data Source
     *
     * @param array $dataSource
     * @return array
     */
    public function prepareDataSource(array $dataSource) {
        $dataSource = parent::prepareDataSource($dataSource);
        
        if(isset($dataSource['data']['items'])) {
            foreach($dataSource['data']['items'] as &$item) {
                $name = $this->getData('name');
                
                if(empty($item['subscription_last_order']) || empty($item['last_order_id'])) {
                    $item[$name] = '';
                } else {
                    $item[$name] = '<a target="_blank" href="' . $this->url->getUrl('sales/order/view', ['order_id' => $item['last_order_id']]) . '">' . $item['subscription_last_order'] . '</a>';
                }
            }
        }

        return $dataSource;
    }
    
}
