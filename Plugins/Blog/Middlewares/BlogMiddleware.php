<?php

namespace Blog\Middlewares;

use Blog\Enums\BlogPermission;
use Oforge\Engine\Modules\Auth\Services\AuthService;
use Oforge\Engine\Modules\Auth\Services\PermissionService;
use Oforge\Engine\Modules\Core\Exceptions\ServiceNotFoundException;
use Oforge\Engine\Modules\Core\Helper\RouteHelper;
use Slim\Http\Request;
use Slim\Http\Response;

/**
 * Class BlogMiddleware
 *
 * @package Blog\Middlewares
 */
class BlogMiddleware {

    /**
     * @param Request $request
     * @param Response $response
     *
     * @return Response|null
     */
    public function prepend(Request $request, Response $response) : ?Response {
        $route            = Oforge()->View()->get('meta.route');
        $controllerClass  = $route['controllerClass'];
        $controllerMethod = $route['controllerMethod'];
        try {
            $user = Oforge()->View()->get('current_user');
            if (!isset($user)) {
                /** @var AuthService $authService */
                $authService = Oforge()->Services()->get("auth");

                $auth = isset($_SESSION['auth']) ? $_SESSION['auth'] : null;
                $user = $authService->decode($auth);
            }
            if (isset($user)) {
                /** @var PermissionService $permissionService */
                $permissionService = Oforge()->Services()->get('permissions');
                $permissions       = $permissionService->get($controllerClass, $controllerMethod);
                if (isset($permissions)) {
                    if ($this->isValidPermission($user, $permissions)) {
                        //nothing to do. proceed
                    } else {
                        Oforge()->View()->assign(['stopNext' => true]);

                        return RouteHelper::redirect($response, 'frontend_blog_view', $route['params']);
                    }

                }
            }
        } catch (ServiceNotFoundException $exception) {
            Oforge()->Logger()->logException($exception);
        }

        return $response;
    }

    /**
     * @param array|null $user
     * @param array|null $permissions
     *
     * @return bool
     */
    private function isValidPermission(?array $user, ?array $permissions) : bool {
        return !isset($permissions['role']) || $permissions['role'] === BlogPermission::PUBLIC || isset($user);
    }

}
