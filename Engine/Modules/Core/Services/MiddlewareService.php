<?php

namespace Oforge\Engine\Modules\Core\Services;

use Doctrine\ORM\ORMException;
use InvalidArgumentException;
use Oforge\Engine\Modules\Core\Abstracts\AbstractDatabaseAccess;
use Oforge\Engine\Modules\Core\Exceptions\ConfigOptionKeyNotExistException;
use Oforge\Engine\Modules\Core\Helper\StringHelper;
use Oforge\Engine\Modules\Core\Models\Endpoint\Endpoint;
use Oforge\Engine\Modules\Core\Models\Plugin\Middleware;

/**
 * Class MiddlewareService
 *
 * @package Oforge\Engine\Modules\Core\Services
 */
class MiddlewareService extends AbstractDatabaseAccess {

    public function __construct() {
        parent::__construct(Middleware::class);
    }

    /**
     * @return Middleware[]
     * @throws ORMException
     */
    public function getActiveMiddlewares() {
        /** @var Middleware[] $middlewares */
        $middlewares = $this->repository()->findBy(['active' => true], ['position' => 'DESC']);

        return $middlewares;
    }

    /**
     * @param Middleware[] $middlewares
     * @param Endpoint $endpoint
     *
     * @return Middleware[]
     */
    public function filterActiveMiddlewaresForEndpoint(array $middlewares, Endpoint $endpoint) {
        $endpointMiddlewares = [];
        foreach ($middlewares as $middleware) {
            $middlewarePath = $middleware->getName();
            if ($middlewarePath === '*' || StringHelper::startsWith($endpoint->getName(), $middlewarePath)) {
                $endpointMiddlewares[] = $middleware;
            }
            // if ($middlewarePath === '*' || preg_match($middlewarePath, $endpoint->getName())) {
            //     $endpointMiddlewares[] = $middleware;
            // }
        }

        return $endpointMiddlewares;
    }

    /**
     * @param array $middlewareConfigs
     * @param bool $active
     *
     * @return Middleware[]
     * @throws ConfigOptionKeyNotExistException
     */
    public function install(array $middlewareConfigs, bool $active = false) {
        /** @var Middleware[] $result */
        $result = [];
        $this->iterateMiddlewareConfigs($middlewareConfigs, function ($pathname, $middlewareConfig) use ($result, $active) {
            if ($this->isValid($middlewareConfig)) {
                /** @var Middleware $middleware */
                $middleware = $this->repository()->findOneBy(['name' => $pathname, 'class' => $middlewareConfig['class']]);
                if ($middleware === null) {
                    $element = Middleware::create([
                        'name'     => $pathname,
                        'class'    => $middlewareConfig['class'],
                        'active'   => $active,
                        'position' => $middlewareConfig['position'],
                    ]);

                    $this->entityManager()->create($element);
                }
                $result[] = $middleware;
            }
        });

        return $result;
    }

    public function uninstall(array $middlewareConfigs) {
        $this->iterateMiddlewareConfigs($middlewareConfigs, function ($pathname, $middlewareConfig) {
            /** @var Middleware $middleware */
            $middleware = $this->repository()->findOneBy(['name' => $pathname, 'class' => $middlewareConfig['class']]);
            if ($middleware !== null) {
                $this->entityManager()->remove($middleware);
            }
        });
    }

    /**
     * @param array $middlewareConfigs
     */
    public function activate(array $middlewareConfigs) {
        $this->updateActive($middlewareConfigs, true);
    }

    /**
     * @param array $middlewareConfigs
     */
    public function deactivate(array $middlewareConfigs) {
        $this->updateActive($middlewareConfigs, false);
    }

    /**
     * Iteration helper for middleware config from Bootstraps.
     *
     * @param array|null $middlewareConfigs
     * @param callable $callable
     */
    private function iterateMiddlewareConfigs(?array $middlewareConfigs, callable $callable) {
        if (!empty($middlewareConfigs)) {
            foreach ($middlewareConfigs as $pathname => $middlewareConfig) {
                if (isset($middlewareConfig['class'])) {
                    $callable($pathname, $middlewareConfig);
                } elseif (is_array($middlewareConfig)) {
                    foreach ($middlewareConfig as $key => $subMiddlewareConfig) {
                        $callable($pathname, $subMiddlewareConfig);
                    }
                }
            }
        }
    }

    /**
     * @param array $options
     *
     * @return bool
     * @throws ConfigOptionKeyNotExistException
     */
    private function isValid(array $options) {
        // Check if required keys are within the options
        $keys = ['class'];
        foreach ($keys as $key) {
            if (!isset($options[$key])) {
                throw new ConfigOptionKeyNotExistException($key);
            }
        }
        // Check if correct type are set
        if (isset($options['position']) && !is_integer($options['position'])) {
            throw new InvalidArgumentException('Position value should be of type integer.');
        }

        return true;
    }

    /**
     * @param array $middlewareConfigs
     * @param bool $active
     */
    private function updateActive(array $middlewareConfigs, bool $active) {
        $this->iterateMiddlewareConfigs($middlewareConfigs, function ($pathname, $middlewareConfig) use ($active) {
            $middleware = $this->repository()->findOneBy(['name' => $pathname, 'class' => $middlewareConfig['class']]);
            if ($middleware !== null) {
                $middleware->setActive($active);
                $this->entityManager()->update($middleware);
            }
        });
    }

}
