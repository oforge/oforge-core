<?php

namespace Oforge\Engine\Modules\Core\Manager\Slim;

use Doctrine\ORM\ORMException;
use Oforge\Engine\Modules\Core\Exceptions\ServiceNotFoundException;
use Oforge\Engine\Modules\Core\Forge\ForgeSlimApp;
use Oforge\Engine\Modules\Core\Middleware\DebugModeMiddleware;
use Oforge\Engine\Modules\Core\Middleware\SessionMiddleware;
use Oforge\Engine\Modules\Core\Models\Endpoint\Endpoint;
use Oforge\Engine\Modules\Core\Models\Endpoint\EndpointMethod;
use Oforge\Engine\Modules\Core\Models\Plugin\Middleware;
use Oforge\Engine\Modules\Core\Services\EndpointService;
use Oforge\Engine\Modules\Core\Services\MiddlewareService;
use Psr\Container\ContainerInterface;
use Slim\Interfaces\RouteInterface;

/**
 * Class RouteManager
 *
 * @package Oforge\Engine\Modules\Core\Manager\Slim
 */
class SlimRouteManager {
    /** @var SlimRouteManager $instance */
    protected static $instance = null;

    /** @return SlimRouteManager */
    public static function getInstance() : SlimRouteManager {
        if (!isset(self::$instance)) {
            self::$instance = new SlimRouteManager();
        }

        return self::$instance;
    }

    /**
     * Make all routes, that come from the database, work
     *
     * @throws ServiceNotFoundException
     * @throws ORMException
     */
    public function init() {
        /** @var ForgeSlimApp $forgeSlimApp */
        $forgeSlimApp = Oforge()->App();
        /** @var ContainerInterface $container */
        $container = $forgeSlimApp->getContainer();
        /** @var MiddlewareService $middlewareService */
        $middlewareService = Oforge()->Services()->get('middleware');
        /** @var EndpointService $endpointService */
        $endpointService = Oforge()->Services()->get('endpoint');
        /** @var Middleware[] $activeMiddlewares */
        $activeMiddlewares = $middlewareService->getActiveMiddlewares();
        /** @var Endpoint[] $endpoints */
        $endpoints = $endpointService->getActiveEndpoints();

        foreach ($endpoints as $endpoint) {
            $httpMethod = $endpoint->getHttpMethod();
            if (!EndpointMethod::isValid($httpMethod)) {
                continue;
            }

            $className   = $endpoint->getControllerClass();
            $classMethod = $endpoint->getControllerMethod();
            if (!$container->has($className)) {
                $container[$className] = function () use ($className) {
                    return new $className();
                };
            }

            /** @var RouteInterface $slimRoute */
            $slimRoute = $forgeSlimApp->$httpMethod(#
                $endpoint->getPath(), $className . ':' . $classMethod#
            )->setName($endpoint->getName());

            $endpointMiddlewares = $middlewareService->filterActiveMiddlewaresForEndpoint($activeMiddlewares, $endpoint);
            $slimRoute->add(new MiddlewarePluginManager($endpointMiddlewares));

            $slimRoute->add(new RenderMiddleware());
            $slimRoute->add(new RouteMiddleware($endpoint));
            $slimRoute->add(new DebugModeMiddleware());
            $slimRoute->add(new SessionMiddleware());
        }
    }

}
