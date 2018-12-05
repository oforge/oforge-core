<?php
namespace Oforge\Engine\Modules\Core\Manager\Services;

use Oforge\Engine\Modules\Core\Exceptions\ServiceAlreadyDefinedException;
use Oforge\Engine\Modules\Core\Exceptions\ServiceNotFoundException;

class ServiceManager {
    protected static $instance = null;
    protected function __construct() {}
    protected function __clone() {}

    public static function getInstance() {
        if (null === self::$instance) {
            self::$instance = new ServiceManager();
        }
        return self::$instance;
    }

    protected $services = [];
    
    /**
     * Find a specific Service by name
     *
     * @param $name
     *
     * @return mixed
     * @throws ServiceNotFoundException
     */
    public function get($name) {
        if(key_exists($name, $this->services)) {
            return $this->services[$name];
        }
        throw new ServiceNotFoundException($name);
    }
    
    /**
     * Register an array of Service names
     *
     * @param array $services
     *
     * @throws ServiceAlreadyDefinedException
     */
    public function register(Array $services) {
        foreach ($services as $name => $className) {
          $this->registerService($name, $className);
        }
    }
    
    /**
     * Register a specific service by name
     *
     * @param $name
     * @param $className
     *
     * @throws ServiceAlreadyDefinedException
     */
    protected function registerService($name, $className) {
        if(key_exists($name, $this->services))  throw new ServiceAlreadyDefinedException($name);

        $this->services[$name] = new $className;
    }
    
    public function listNames() {
        return array_keys($this->services);
    }
}
