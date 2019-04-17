<?php

namespace Oforge\Engine\Modules\Core\Manager\SlimRoutes;

use Doctrine\ORM\ORMException;
use Oforge\Engine\Modules\Core\Exceptions\ServiceNotFoundException;
use Oforge\Engine\Modules\Core\Forge\ForgeSlimApp;
use Oforge\Engine\Modules\Core\Middleware\DebugModeMiddleware;
use Oforge\Engine\Modules\Core\Models\Endpoint\Endpoint;
use Oforge\Engine\Modules\Core\Models\Endpoint\EndpointMethod;
use Oforge\Engine\Modules\Core\Models\Plugin\Middleware;
use Oforge\Engine\Modules\Core\Services\MiddlewareService;
use Oforge\Engine\Modules\Session\Middleware\SessionMiddleware;
use Psr\Container\ContainerInterface;
use Slim\Interfaces\RouteInterface;

/**
 * Class RouteManager
 *
 * @package Oforge\Engine\Modules\Core\Manager\Routes
 */
class SlimRouteManager {
    /** @var SlimRouteManager $instance */
    protected static $instance = null;

    /**
     * @return SlimRouteManager
     */
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
        $entityManager      = Oforge()->DB()->getEnityManager();
        $endpointRepository = $entityManager->getRepository(Endpoint::class);

        /** @var ForgeSlimApp $forgeSlimApp */
        $forgeSlimApp = Oforge()->App();
        /** @var ContainerInterface $container */
        $container = $forgeSlimApp->getContainer();
        /** @var MiddlewareService $middlewareService */
        $middlewareService = Oforge()->Services()->get('middleware');
        /** @var string[] $activeMiddlewareNames */
        $activeMiddlewareNames = $middlewareService->getAllDistinctActiveNames();
        /** @var Endpoint[] $endpoints */
        $endpoints = $endpointRepository->findBy(['active' => 1], ['order' => 'ASC']);

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

            /** @var Middleware[] $activeMiddlewares */
            $activeMiddlewares = [];
            //$activeMiddlewares = $middlewareService->getActive( $endpoint->getName() );
            //$slimRoute->add( new MiddlewarePluginManager( $activeMiddlewares ) );
            foreach ($activeMiddlewareNames as $middlewareName) {
                $pattern = "/^" . $middlewareName . "/";

                if (preg_match($pattern, $endpoint->getName())) {
                    $activeMiddlewares = $middlewareService->getActive($middlewareName);
                    $slimRoute->add(new MiddlewarePluginManager($activeMiddlewares));
                }
            }

            $activeMiddlewares = $middlewareService->getActive('*');
            $slimRoute->add(new MiddlewarePluginManager($activeMiddlewares));
            $slimRoute->add(new RenderMiddleware());
            $slimRoute->add(new RouteMiddleware($endpoint));
            $slimRoute->add(new DebugModeMiddleware());
            $slimRoute->add(new SessionMiddleware());//Designfehler Sessionmodule in core?
        }
    }

}
