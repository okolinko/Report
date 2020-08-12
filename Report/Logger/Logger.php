<?php
namespace Toppik\Report\Logger;

class Logger extends \Monolog\Logger {
	
    public function __construct(
		array $handlers = [],
		array $processors = []
	) {
        parent::__construct('toppikreport', $handlers, $processors);
    }
	
}
