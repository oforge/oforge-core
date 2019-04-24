<?php

namespace Oforge\Engine\Modules\Core\Services;

use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use Oforge\Engine\Modules\Core\Abstracts\AbstractDatabaseAccess;
use Oforge\Engine\Modules\Core\Exceptions\ConfigOptionKeyNotExistsException;
use Oforge\Engine\Modules\Core\Helper\ArrayHelper;
use Oforge\Engine\Modules\Core\Models\Endpoints\Endpoint;

class EndpointService extends AbstractDatabaseAccess {

    public function __construct() {
        parent::__construct(["default" => Endpoint::class]);
    }

    const SLIM_ROUTE_METHODS = ['any', 'get', 'post', 'put', 'patch', 'delete', 'options'];

    /**
     * Store endpoints in a database table
     *
     * @param array $endpoints
     *
     * @throws ORMException
     * @throws OptimisticLockException
     * @throws ConfigOptionKeyNotExistsException
     */
    public function register(array $endpoints) {
        $endpointConfigs = $this->prepareEndpointConfigs($endpoints);

        $created = false;
        foreach ($endpointConfigs as $endpointConfig) {
            /**
             * @var Endpoint $endpoint
             */
            $endpoint = $this->repository()->findOneBy(['name' => $endpointConfig['name']]);
            if (!isset($endpoint)) {
                $endpoint = Endpoint::create($endpointConfig);
                $endpoint->setActive(true);
                $this->entityManager()->persist($endpoint);
                $created = true;
            }
        }

        if ($created) {
            $this->entityManager()->flush();
            $this->repository()->clear();
        }
    }

    /**
     * Remove endpoints
     *
     * @param array $endpoints
     *
     * @throws ConfigOptionKeyNotExistsException
     * @throws ORMException
     */
    public function unregister(array $endpoints) {//TODO ungetestet
        $endpointConfigs = $this->prepareEndpointConfigs($endpoints);

        foreach ($endpointConfigs as $endpointConfig) {
            /**
             * @var Endpoint $endpoint
             */
            $endpoints = $this->repository()->findBy(['controller' => $endpointConfig['controller']]);
            if (count($endpoints) > 0) {
                foreach ($endpoints as $endpoint) {
                    $this->entityManager()->remove($endpoint);
                }
                $this->entityManager()->flush();
            }
            $this->repository()->clear();
        }
    }

    /**
     * @param array $options
     *
     * @return bool
     * @throws ConfigOptionKeyNotExistsException
     */
    private function isValid(array $options) {
        /**
         * Check if required keys are within the options
         */
        $keys = ['controller', 'name'];
        foreach ($keys as $key) {
            if (!array_key_exists($key, $options)) {
                throw new ConfigOptionKeyNotExistsException($key);
            }
        }

        return true;
    }

    /**
     * @param array $endpoints
     *
     * @return array
     * @throws ConfigOptionKeyNotExistsException
     */
    protected function prepareEndpointConfigs(array $endpoints) : array {
        $endpointConfigs = [];

        foreach ($endpoints as $path => $config) {
            $isRoot = ($path === '/' || '');

            if ($this->isValid($config)) {
                $controller = $config['controller'];
                $scope      = ArrayHelper::get($config, 'asset_scope', 'frontend');
                $order      = ArrayHelper::get($config, 'order', 1337);
                $methods    = ArrayHelper::get($config, 'methods', []);

                $classMethods = get_class_methods($config['controller']);

                if ($classMethods === null) {
                    // TODO: Check, if this logger usage is safe!
                    Oforge()->Logger()->get()->addWarning("Maybe some namespace, class or method was defined wrong.");
                    Oforge()->Logger()->get()->addWarning(json_encode($config));
                    $classMethods = [];
                }

                if (!empty($methods)) {
                    $classMethods = array_intersect(array_keys($methods), $classMethods);
                }
                foreach ($classMethods as $classMethod) {
                    if (substr($classMethod, -6) !== 'Action') {
                        continue;
                    }
                    if ($classMethod === 'indexAction' || isset($methods[$classMethod])) {
                        $name       = ArrayHelper::get($config, 'name', str_replace('/', '_', $path));
                        $actionPath = $path;
                    } else {
                        $action = substr($classMethod, 0, -6);
                        if (isset($config['name'])) {
                            $name = $config['name'] . '_' . $action;
                        } else {
                            $name = $path . ($isRoot ? '' : '/') . $action;
                        }
                        $actionPath = $path . ($isRoot ? '' : '/') . $action;
                    }
                    $httpMethod = ArrayHelper::get($methods, $classMethod, 'any');
                    if (!in_array($httpMethod, self::SLIM_ROUTE_METHODS)) {
                        $httpMethod = 'any';
                    }

                    $controllerMethod  = $controller . ':' . $classMethod;
                    $endpointConfigs[] = [
                        'name'        => $name,
                        'path'        => $actionPath,
                        'controller'  => $controllerMethod,
                        'asset_scope' => $scope,
                        'http_method' => $httpMethod,
                        'order'       => $order,
                    ];
                }
                if (empty($endpointConfigs)) {
                    Oforge()->Logger()->get()
                            ->addWarning('An endpoint was defined but the corresponding controller has no method that ends with ...Action', $config);
                }
            }
        }

        return $endpointConfigs;
    }
}
