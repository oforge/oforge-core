<?php

namespace Oforge\Engine\Modules\AdminBackend\Controller\Backend;

use Oforge\Engine\Modules\AdminBackend\Abstracts\SecureBackendController;
use Oforge\Engine\Modules\Auth\Models\User\BackendUser;
use Oforge\Engine\Modules\Session\Services\SessionManagementService;
use Slim\Http\Request;
use Slim\Http\Response;
use Slim\Router;

class FavoritesController extends SecureBackendController
{
    /**
     * @param Request $request
     * @param Response $response
     *
     * @return Response
     * @throws \Exception
     */
    public function toggleAction(Request $request, Response $response) {

        /** @var $authService AuthService */
        $authService = Oforge()->Services()->get("auth");
        $user        = $authService->decode($_SESSION["auth"]);

        $name = $request->getQueryParam("name");

        if (isset($user) && isset($user['id']) && !empty($name)) {
            /** @var UserFavoritesService $favoritesService */
            $favoritesService = Oforge()->Services()->get("backend.favorites");

            $favoritesService->toggle($user['id'], $name);

            $uri = Oforge()->App()->getContainer()->get('router')->pathFor($name);

            return $response->withRedirect($uri, 302);
        }
    }
    
    public function initPermissions() {
        $this->ensurePermissions("toggleAction", BackendUser::class, BackendUser::ROLE_MODERATOR);
    }
}
