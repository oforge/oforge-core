<?php

namespace Oforge\Engine\Modules\Auth\Middleware;

use Oforge\Engine\Modules\Auth\Services\AuthService;
use Oforge\Engine\Modules\Auth\Services\PermissionService;
use Oforge\Engine\Modules\Core\Exceptions\ServiceNotFoundException;
use Oforge\Engine\Modules\Core\Helper\RedirectHelper;
use Slim\Http\Request;
use Slim\Http\Response;

/**
 * Class SecureMiddleware
 *
 * @package Oforge\Engine\Modules\Auth\Middleware
 */
class SecureMiddleware {
    /** @var string $urlPathName The named path for redirects */
    protected $urlPathName = '';

    /**
     * Middleware call before the controller call
     *
     * @param Request $request
     * @param Response $response
     *
     * @return Response|null
     * @throws ServiceNotFoundException
     */
    public function prepend($request, $response) : ?Response {
        $routeController  = Oforge()->View()->get('meta')['route'];
        $controllerClass  = $routeController['controllerClass'];
        $controllerMethod = $routeController['controllerMethod'];

        /**
         * @var $permissionService PermissionService
         */
        $permissionService = Oforge()->Services()->get('permissions');

        $permissions = $permissionService->get($controllerClass, $controllerMethod);
        $auth        = null;

        if (isset($_SESSION['auth'])) {
            $auth = $_SESSION['auth'];
        }

        if (isset($permissions)) {
            /** @var AuthService $authService */
            $authService = Oforge()->Services()->get('auth');
            $user        = $authService->decode($auth);

            if ($this->isUserValid($user, $permissions)) {
                Oforge()->View()->assign([
                    'user' => $user,
                ]);
                //nothing to do. proceed
            } else {
                /*
                TODO: If there is a secured area, there should be either a message when redirecting
                      or a 401 status code.
                */

                Oforge()->View()->assign(['stopNext' => true]);

                if (!empty($this->urlPathName)) {
                    return RedirectHelper::redirect($response, $this->urlPathName);
                }

                return $response = $response->withRedirect('/', 302);
            }
        }

        return $response;
    }

    /**
     * @param $user
     * @param $permissions
     *
     * @return bool
     */
    protected function isUserValid($user, $permissions) {
        return (!is_null($user)
                && isset($user['role'])
                && $user['role'] <= $permissions['role']
                && isset($user['type'])
                && $user['type'] == $permissions['type']);
    }

}
