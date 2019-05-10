<?php

namespace Oforge\Engine\Modules\Core\Manager\Services;

use Oforge\Engine\Modules\Core\Exceptions\ServiceAlreadyExistException;
use Oforge\Engine\Modules\Core\Exceptions\ServiceNotFoundException;

/**
 * Class ServiceManager
 *
 * @package Oforge\Engine\Modules\Core\Manager\Services
 */
class ServiceManager {
    /**
     * @var ServiceManager $instance
     */
    protected static $instance = null;
    /**
     * @var array $services
     */
    protected $services = [];

    protected function __construct() {
    }

    /**
     * @return ServiceManager
     */
    public static function getInstance() {
        if (null === self::$instance) {
            self::$instance = new ServiceManager();
        }

        return self::$instance;
    }

    /**
     * Find a specific service by name.
     *
     * @param $name
     *
     * @return mixed
     * @throws ServiceNotFoundException
     */
    public function get($name) {
        if (isset($this->services[$name])) {
            return $this->services[$name];
        }

        throw new ServiceNotFoundException($name);
    }

    /**
     * Get all (unsorted) service names.
     *
     * @return string[]
     */
    public function getServiceNames() {
        return array_keys($this->services);
    }

    /**
     * Register an array of services. Array of name-classname-pairs.
     *
     * @param array $services
     *
     * @throws ServiceAlreadyExistException
     */
    public function register(array $services) {
        foreach ($services as $name => $className) {
            $this->registerService($name, $className);
        }
    }

    /**
     * Register a specific service by name
     *
     * @param string $name
     * @param string $className
     *
     * @throws ServiceAlreadyExistException
     */
    protected function registerService(string $name, string $className) {
        if (isset($this->services[$name])) {
            throw new ServiceAlreadyExistException($name);
        }

        $this->services[$name] = new $className();
    }

    protected function __clone() {

    }

}
