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

        if (isset($permissions)) {

            $authCookie = $request->getCookieParam("authorization");
            /**
             * @var $authService AuthService
             */
            $authService = Oforge()->Services()->get("auth");
            $user = $authService->decode($authCookie);


            if (isset($user) &&
                isset($user["user_role"]) && $user["user_role"] <= $permissions["user_role"] &&
                isset($user["user_type"]) && $user["user_type"] == $permissions["user_type"]

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