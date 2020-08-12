<?php
namespace Toppik\Report\Model;

class TransportBuilder extends \Magento\Framework\Mail\Template\TransportBuilder {
	
    /**
     * Reset object state
     *
     * @return $this
     */
    public function reset() {
        $this->message = $this->objectManager->create('Magento\Framework\Mail\Message');
        $this->templateIdentifier = null;
        $this->templateVars = null;
        $this->templateOptions = null;
        return $this;
    }
	
    /**
     * @param string $filename
     * @param string $content
     * @return $this
     */
    public function attachFile($filename, $content) {
        if(!empty($filename) && !empty($content) && is_string($filename) && is_string($content)) {
            $this->message
                ->createAttachment(
                    $content,
                    \Zend_Mime::TYPE_OCTETSTREAM,
                    \Zend_Mime::DISPOSITION_ATTACHMENT,
                    \Zend_Mime::ENCODING_BASE64,
                    $filename
                );
        }
		
        return $this;
    }
	
}
