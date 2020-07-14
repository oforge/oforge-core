<?php

namespace Oforge\Engine\Modules\AdminBackend\Core\Middleware;

use Oforge\Engine\Modules\Auth\Models\User\BackendUser;
use Oforge\Engine\Modules\Auth\Services\AuthService;
use Oforge\Engine\Modules\Core\Exceptions\ServiceNotFoundException;
use Slim\Http\Request;
use Slim\Http\Response;

/**
 * Class FrontendBackendPopupMiddleware
 *
 * @package Oforge\Engine\Modules\AdminBackend\Core\Middleware
 */
class FrontendBackendPopupMiddleware {

    /**
     * Middleware call before the controller call
     *
     * @param Request $request
     * @param Response $response
     *
     * @return Response|null
     * @throws ServiceNotFoundException
     */
    public function prepend(Request $request, Response $response) : ?Response {
        $route = Oforge()->View()->get('meta.route');
        if ($route !== null) {
            $showPopup = false;
            if (isset($_SESSION['auth'])) {
                $auth = $_SESSION['auth'];
                /** @var AuthService $authService */
                $authService = Oforge()->Services()->get('auth');
                $user        = $authService->decode($auth);
                if ($user !== null && $user['type'] === BackendUser::class) {
                    $showPopup = true;
                }
            }
            Oforge()->View()->assign([
                'meta.backend.logged' => $showPopup,
            ]);
        }

        return $response;
    }
}
