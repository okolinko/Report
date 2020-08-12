<?php
namespace Toppik\Report\Block\Adminhtml\Widget\Grid\Column\Renderer\Subscription\Index;

class TotalActiveMs extends \Toppik\Report\Block\Adminhtml\Widget\Grid\Column\Renderer\Subscription\AbstractClass {
    
    protected function _getValueUrl($row) {
        $params = [
                'status'                        => urlencode(\Toppik\Subscriptions\Model\Profile::STATUS_ACTIVE),
                'subscription_merchant_source'  => urlencode('ms'),
                'created_at_from'               => urlencode($row->getData('period_from')),
                'created_at_to'                 => urlencode($row->getData('period_to'))
        ];
        
        return $this->getUrl(
            'toppikreport/subscription/index_detail',
            [
                '_query' => http_build_query($params)
            ]
        );
    }
    
}
