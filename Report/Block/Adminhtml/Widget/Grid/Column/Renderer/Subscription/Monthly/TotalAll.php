<?php
namespace Toppik\Report\Block\Adminhtml\Widget\Grid\Column\Renderer\Subscription\Monthly;

class TotalAll extends \Toppik\Report\Block\Adminhtml\Widget\Grid\Column\Renderer\Subscription\AbstractClass {
    
    protected function _getValueUrl($row) {
        $params = [
                'created_at_from'   => urlencode($row->getData('period_from')),
                'created_at_to'     => urlencode($row->getData('period_to'))
        ];
        
        return $this->getUrl(
            'toppikreport/subscription/monthly_detail',
            [
                '_query' => http_build_query($params)
            ]
        );
    }
    
}
