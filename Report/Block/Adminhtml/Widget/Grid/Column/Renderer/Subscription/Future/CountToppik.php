<?php
namespace Toppik\Report\Block\Adminhtml\Widget\Grid\Column\Renderer\Subscription\Future;

class CountToppik extends \Toppik\Report\Block\Adminhtml\Widget\Grid\Column\Renderer\Subscription\AbstractClass {
    
    protected function _getValueUrl($row) {
        $params = [
                'date'                          => urlencode($row->getData('date')),
                'subscription_merchant_source'  => urlencode(\Toppik\OrderSource\Model\Merchant\Source::SOURCE_0)
        ];
        
        return $this->getUrl(
            'toppikreport/subscription/future_detail',
            [
                '_query' => http_build_query($params)
            ]
        );
    }
    
}
