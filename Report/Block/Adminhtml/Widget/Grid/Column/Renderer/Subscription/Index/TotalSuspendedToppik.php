<?php
namespace Toppik\Report\Block\Adminhtml\Widget\Grid\Column\Renderer\Subscription\Index;

class TotalSuspendedToppik extends \Toppik\Report\Block\Adminhtml\Widget\Grid\Column\Renderer\Subscription\AbstractClass {
    
    protected function _getValueUrl($row) {
        $params = [
                'status'                        => urlencode(\Toppik\Subscriptions\Model\Profile::STATUS_SUSPENDED),
                'subscription_merchant_source'  => urlencode(\Toppik\OrderSource\Model\Merchant\Source::SOURCE_0),
                'suspended_at_from'             => urlencode($row->getData('period_from')),
                'suspended_at_to'               => urlencode($row->getData('period_to'))
        ];
        
        return $this->getUrl(
            'toppikreport/subscription/index_detail',
            [
                '_query' => http_build_query($params)
            ]
        );
    }
    
}
