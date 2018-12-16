<?php

namespace Oforge\Engine\Modules\AdminBackendSystem\Controller\Backend\System;

use Doctrine\ORM\EntityManager;
use Oforge\Engine\Modules\AdminBackend\Abstracts\SecureBackendController;
use Oforge\Engine\Modules\Core\Models\Routes\Route;
use Slim\Http\Request;
use Slim\Http\Response;

class RoutesController extends SecureBackendController
{
    public function indexAction(Request $request, Response $response) {
        /**
         * @var EntityManager $entityManager
         */
        $entityManager = Oforge()->DB()->getManager();

        $routeRepository = $entityManager->getRepository(Route::class);
        /**
         * @var Route[] $routes
         */
        $routes = $routeRepository->findAll();
        $routes = array_map(function(Route $route) {
            return $route->toArray();
        }, $routes);
        $routes = array("routes" => $routes);

        Oforge()->View()->assign($routes);
    }
}