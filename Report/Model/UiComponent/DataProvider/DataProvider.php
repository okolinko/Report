<?php
namespace Toppik\Report\Model\UiComponent\DataProvider;

class DataProvider extends \Magento\Framework\View\Element\UiComponent\DataProvider\DataProvider {
    
    /**
     * @return void
     */
    protected function prepareUpdateUrl() {
        if(!isset($this->data['config']['filter_url_params'])) {
            return;
        }
        
        foreach($this->data['config']['filter_url_params'] as $paramName => $paramValue) {
            if('*' == $paramValue) {
                $paramValue = urldecode($this->request->getParam($paramName));
            }
            
            if($paramValue) {
                $this->data['config']['update_url'] = sprintf(
                    '%s%s/%s/',
                    $this->data['config']['update_url'],
                    $paramName,
                    $paramValue
                );
                
                $condition = 'in';
                
                if(strpos($paramName, '_from') !== false) {
                    $condition = 'gteq';
                    $paramName = str_replace('_from', '', $paramName);
                } else if(strpos($paramName, '_to') !== false) {
                    $condition = 'lteq';
                    $paramName = str_replace('_to', '', $paramName);
                } else if($paramValue == 'ms') {
                    $condition = 'nin';
                    $paramValue = \Toppik\OrderSource\Model\Merchant\Source::SOURCE_0;
                }
                
                $this->addFilter(
                    $this->filterBuilder->setField($paramName)->setValue($paramValue)->setConditionType($condition)->create()
                );
            }
        }
    }
    
}
