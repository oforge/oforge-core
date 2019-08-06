<?php

namespace Oforge\Engine\Modules\AdminBackend\Core\Controller\Backend;

use Exception;
use Oforge\Engine\Modules\AdminBackend\Core\Abstracts\SecureBackendController;
use Oforge\Engine\Modules\Auth\Models\User\BackendUser;
use Oforge\Engine\Modules\Core\Annotation\Endpoint\EndpointAction;
use Oforge\Engine\Modules\Core\Annotation\Endpoint\EndpointClass;
use Oforge\Engine\Modules\Core\Helper\RouteHelper;
use Oforge\Engine\Modules\Core\Services\Session\SessionManagementService;
use Slim\Http\Request;
use Slim\Http\Response;

/**
 * Class LogoutController
 *
 * @package Oforge\Engine\Modules\AdminBackend\Core\Controller\Backend
 * @EndpointClass(path="/backend/logout", name="backend_logout", assetScope="Backend")
 */
class LogoutController extends SecureBackendController {

    /**
     * @param Request $request
     * @param Response $response
     *
     * @return Response
     * @throws Exception
     * @EndpointAction()
     */
    public function indexAction(Request $request, Response $response) {
        /** @var SessionManagementService $sessionManager */
        $sessionManager = Oforge()->Services()->get('session.management');
        $sessionManager->sessionDestroy();

        return RouteHelper::redirect($response, 'backend_login');
    }

    public function initPermissions() {
        $this->ensurePermission('indexAction', BackendUser::ROLE_MODERATOR);
    }

}
