<?php
namespace Toppik\Report\Ui\Component\Listing\Column\System;

class Type extends \Magento\Ui\Component\Listing\Columns\Column {
    
    /**
     * @var UrlInterface
     */
    protected $urlBuilder;
    
    /**
     * @param \Magento\Framework\View\Element\UiComponent\ContextInterface $context
     * @param \Magento\Framework\View\Element\UiComponentFactory $uiComponentFactory
     * @param \Magento\Framework\UrlInterface $urlBuilder
     * @param array $components
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\View\Element\UiComponent\ContextInterface $context,
        \Magento\Framework\View\Element\UiComponentFactory $uiComponentFactory,
        \Magento\Framework\UrlInterface $urlBuilder,
        array $components = [],
        array $data = []
    ) {
        $this->urlBuilder = $urlBuilder;
        parent::__construct($context, $uiComponentFactory, $components, $data);
    }
    
    /**
     * Prepare Data Source
     *
     * @param array $dataSource
     * @return array
     */
    public function prepareDataSource(array $dataSource) {
        if(isset($dataSource['data']['items'])) {
            foreach($dataSource['data']['items'] as &$item) {
                $name = $this->getData('name');
                
                if(empty($item['entity_type_label']) || empty($item['entity_type_id'])) {
                    $item[$name] = '';
                } else {
                    $item[$name] = '<a target="_blank" href="' . $this->urlBuilder->getUrl('toppikreport/system/integrations_entity_view', ['id' => $item['entity_type_id']]) . '">' . $item['entity_type_label'] . '</a>';
                }
            }
        }
        
        return $dataSource;
    }
    
}
