<?php

namespace Oforge\Engine\Modules\AdminBackend\Core\Controller\Backend;

use Exception;
use Oforge\Engine\Modules\AdminBackend\Core\Abstracts\SecureBackendController;
use Oforge\Engine\Modules\AdminBackend\Core\Services\UserFavoritesService;
use Oforge\Engine\Modules\Auth\Models\User\BackendUser;
use Oforge\Engine\Modules\Core\Annotation\Endpoint\EndpointAction;
use Oforge\Engine\Modules\Core\Annotation\Endpoint\EndpointClass;
use Oforge\Engine\Modules\Core\Helper\RouteHelper;
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
     * @EndpointAction()
     */
    public function toggleAction(Request $request, Response $response) {
        $user      = Oforge()->View()->get('user');
        $routeName = $request->getQueryParam('name');

        if (isset($user['id']) && isset($routeName)) {
            try {
                /** @var UserFavoritesService $favoritesService */
                $favoritesService = Oforge()->Services()->get('backend.favorites');
                $favoritesService->toggle($user['id'], $routeName);
                $routeParams      = $request->getQueryParam('params', []);
                $routeQueryParams = $request->getQueryParam('query', []);

                return RouteHelper::redirect($response, $routeName, $routeParams, $routeQueryParams);
            } catch (Exception $exception) {
                Oforge()->Logger()->logException($exception);
            }
        }
    }

    public function initPermissions() {
        $this->ensurePermission('toggleAction', BackendUser::ROLE_MODERATOR);
    }

}
