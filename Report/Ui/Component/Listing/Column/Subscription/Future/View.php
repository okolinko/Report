<?php
namespace Toppik\Report\Ui\Component\Listing\Column\Subscription\Future;

class View extends \Magento\Ui\Component\Listing\Columns\Column {
    
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
        if (isset($dataSource['data']['items'])) {
            foreach($dataSource['data']['items'] as &$item) {
                $name = $this->getData('name');
                
                if(empty($item['subscription_id'])) {
                    $item[$name] = '';
                } else {
                    $item[$name] = '<a target="_blank" href="' . $this->url->getUrl('subscriptions/profiles/view', ['profile_id' => $item['subscription_id']]) . '">' . $item['subscription_id'] . '</a>';
                }
            }
        }
        
        return $dataSource;
    }
    
}
