<?php

namespace Oforge\Engine\Modules\AdminBackend\Core\Controller\Backend;

use Exception;
use Oforge\Engine\Modules\AdminBackend\Core\Abstracts\SecureBackendController;
use Oforge\Engine\Modules\Auth\Models\User\BackendUser;
use Oforge\Engine\Modules\Core\Annotation\Endpoint\EndpointAction;
use Oforge\Engine\Modules\Core\Annotation\Endpoint\EndpointClass;
use Oforge\Engine\Modules\Core\Helper\RouteHelper;
use Oforge\Engine\Modules\Core\Manager\SessionManager;
use Slim\Http\Request;
use Slim\Http\Response;

/**
 * Class LogoutController
 *
 * @package Oforge\Engine\Modules\AdminBackend\Core\Controller\Backend
 * @EndpointClass(path="/backend/logout", name="backend_logout", assetScope="Backend")
 */
class LogoutController extends SecureBackendController {

    public function initPermissions() {
        $this->ensurePermission('indexAction', BackendUser::ROLE_LOGGED_IN);
    }

    /**
     * @param Request $request
     * @param Response $response
     *
     * @return Response
     * @throws Exception
     * @EndpointAction()
     */
    public function indexAction(Request $request, Response $response) {
        SessionManager::destroy();

        return RouteHelper::redirect($response, 'backend_login');
    }

}
