<?php

namespace Oforge\Engine\Modules\AdminBackend\Core\Controller\Backend;

use Oforge\Engine\Modules\AdminBackend\Core\Abstracts\SecureBackendController;
use Oforge\Engine\Modules\AdminBackend\Core\Services\UserFavoritesService;
use Oforge\Engine\Modules\Auth\Models\User\BackendUser;
use Oforge\Engine\Modules\Auth\Services\AuthService;
use Oforge\Engine\Modules\Core\Annotation\Endpoint\EndpointAction;
use Oforge\Engine\Modules\Core\Annotation\Endpoint\EndpointClass;
use Slim\Http\Request;
use Slim\Http\Response;

/**
 * Class FavoritesController
 *
 * @package Oforge\Engine\Modules\AdminBackend\Core\Controller\Backend
 * @EndpointClass(path="/backend/favorites", name="backend_favorites", assetScope="Backend")
 */
class FavoritesController extends SecureBackendController {

    /**
     * @param Request $request
     * @param Response $response
     *
     * @return Response
     * @throws \Exception
     * @EndpointAction()
     */
    public function toggleAction(Request $request, Response $response) {
        /** @var AuthService $authService */
        $authService = Oforge()->Services()->get('auth');
        $user        = $authService->decode($_SESSION['auth']);
        $name        = $request->getQueryParam('name');

        if (isset($user) && isset($user['id']) && !empty($name)) {
            /** @var UserFavoritesService $favoritesService */
            $favoritesService = Oforge()->Services()->get('backend.favorites');
            $favoritesService->toggle($user['id'], $name);
            $uri = Oforge()->App()->getContainer()->get('router')->pathFor($name);

            return $response->withRedirect($uri, 302);
        }
    }

    public function initPermissions() {
        $this->ensurePermissions('toggleAction', BackendUser::class, BackendUser::ROLE_MODERATOR);
    }

}
