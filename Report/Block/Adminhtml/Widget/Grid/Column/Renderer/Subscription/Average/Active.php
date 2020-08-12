<?php
namespace Toppik\Report\Block\Adminhtml\Widget\Grid\Column\Renderer\Subscription\Average;

class Active extends \Toppik\Report\Block\Adminhtml\Widget\Grid\Column\Renderer\Subscription\AbstractClass {
    
    protected function _getValueUrl($row) {
        $params = [
                'subscription_status'           => urlencode(\Toppik\Subscriptions\Model\Profile::STATUS_ACTIVE),
                'subscription_merchant_source'  => urlencode($row->getData('merchant_source')),
                'subscription_period'           => urlencode($row->getData('subscription_period') * 60 * 60 * 24)
        ];
        
        return $this->getUrl(
            'toppikreport/subscription/average_detail',
            [
                '_query' => http_build_query($params)
            ]
        );
    }
    
}
