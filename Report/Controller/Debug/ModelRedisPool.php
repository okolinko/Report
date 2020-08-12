<?php
namespace Toppik\Report\Controller\Debug;

use Magento\Framework\App\Cache\Type\FrontendPool;
use Magento\Framework\App\DeploymentConfig;

class ModelRedisPool extends \Magento\Framework\App\Cache\Frontend\Pool {
    
    /**
     * Frontend identifier associated with the default settings
     */
    const DEFAULT_FRONTEND_ID = 'default';

    /**
     * @var DeploymentConfig
     */
    protected $deploymentConfig;

    /**
     * @var Factory
     */
    protected $_factory;

    /**
     * @var \Magento\Framework\Cache\FrontendInterface[]
     */
    protected $_instances;

    /**
     * @var array
     */
    protected $_frontendSettings;

    /**
     * @param DeploymentConfig $deploymentConfig
     * @param Factory $frontendFactory
     * @param array $frontendSettings Format: array('<frontend_id>' => array(<cache_settings>), ...)
     */
    public function __construct(
        DeploymentConfig $deploymentConfig,
        \Magento\Framework\App\Cache\Frontend\Factory $frontendFactory,
        array $frontendSettings = []
    ) {
        $this->deploymentConfig = $deploymentConfig;
        $this->_factory = $frontendFactory;
        $this->_frontendSettings = $frontendSettings + [self::DEFAULT_FRONTEND_ID => []];
    }

    /**
     * Create instances of every cache frontend known to the system.
     * Method is to be used for delayed initialization of the iterator.
     *
     * @return void
     */
    protected function _initialize()
    {
        if ($this->_instances === null) {
            $this->_instances = [];
            foreach ($this->_getCacheSettings() as $frontendId => $frontendOptions) {
                $this->_instances[$frontendId] = $this->_factory->create($frontendOptions);
                // echo '<pre>';print_r($frontendOptions);
            }
            
            exit;
            
        }
    }

    /**
     * Retrieve settings for all cache front-ends known to the system
     *
     * @return array Format: array('<frontend_id>' => array(<cache_settings>), ...)
     
    protected function _getCacheSettings() {
        return array_merge($this->_frontendSettings, array(
            'default' => array (
                'backend' => 'Cm_Cache_Backend_Redis',
                'backend_options' => array (
                    'server' => '127.0.0.1',
                    'port' => '6379',
                    'database' => '2'
                )
            ),
            'page_cache' => array (
                'backend' => 'Cm_Cache_Backend_Redis',
                'backend_options' => array (
                    'server' => '127.0.0.1',
                    'port' => '6379',
                    'database' => '3',
                    'compress_data' => '0'
                )
            )
        ));
    }*/
    
    /**
     * Retrieve frontend instance by its unique identifier
     *
     * @param string $identifier Cache frontend identifier
     * @return \Magento\Framework\Cache\FrontendInterface Cache frontend instance
     * @throws \InvalidArgumentException
     */
    public function get($identifier)
    {
        $this->_initialize();
        if (isset($this->_instances[$identifier])) {
            return $this->_instances[$identifier];
        }
        throw new \InvalidArgumentException("Cache frontend '{$identifier}' is not recognized.");
    }
}
