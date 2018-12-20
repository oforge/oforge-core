<?php

namespace Oforge\Engine\Modules\AdminBackend\Middleware;

use Oforge\Engine\Modules\AdminBackend\Services\Permissions;
use Oforge\Engine\Modules\Auth\Services\AuthService;
use Slim\Http\Request;
use Slim\Http\Response;

/**
 * Created by PhpStorm.
 * User: Matthaeus.Schmedding
 * Date: 12.12.2018
 * Time: 10:07
 */
class BackendSecureMiddleware
{
    /**
     * Middleware call before the controller call
     *
     * @param Request $request
     * @param Response $response
     *
     * @return ?Response
     * @throws \Oforge\Engine\Modules\Core\Exceptions\ServiceNotFoundException
     */
    public function prepend($request, $response)
    {
        $controllerMethod = Oforge()->View()->get("meta")["controller_method"];

        /**
         * @var $permissionService Permissions
         */
        $permissionService = Oforge()->Services()->get("permissions");

        $permissions = $permissionService->get($controllerMethod);
        $auth = null;
        if (isset($_SESSION['auth'])) {
            $auth = $_SESSION['auth'];
        }

        if (isset($permissions)) {
            
            /**
             * @var $authService AuthService
             */
            $authService = Oforge()->Services()->get("auth");
            $user = $authService->decode($auth);


            if (isset($user) &&
                isset($user["role"]) && $user["role"] <= $permissions["role"] &&
                isset($user["type"]) && $user["type"] == $permissions["type"]

            ) {
                //nothing to do. proceed
            } else {
                /**
                 * @var $router Router
                 */
                $router = Oforge()->App()->getContainer()->get("router");
                $uri = $router->pathFor("backend_login");

                return $response->withRedirect($uri, 302);
            }
        }
    }
}