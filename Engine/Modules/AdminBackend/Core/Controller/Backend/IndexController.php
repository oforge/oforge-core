<?php

namespace Oforge\Engine\Modules\AdminBackend\Core\Controller\Backend;

use Oforge\Engine\Modules\AdminBackend\Core\Abstracts\SecureBackendController;
use Oforge\Engine\Modules\Auth\Models\User\BackendUser;
use Oforge\Engine\Modules\Core\Annotation\Endpoint\EndpointAction;
use Oforge\Engine\Modules\Core\Annotation\Endpoint\EndpointClass;
use Oforge\Engine\Modules\Core\Helper\RouteHelper;
use Slim\Http\Request;
use Slim\Http\Response;

/**
 * Class IndexController
 *
 * @package Oforge\Engine\Modules\AdminBackend\Core\Controller\Backend
 * @EndpointClass(path="/backend[/]", name="backend", assetScope="Backend")
 */
class IndexController extends SecureBackendController {

    public function initPermissions() {
        $this->ensurePermission('indexAction', BackendUser::ROLE_PUBLIC);
    }

    /**
     * @param Request $request
     * @param Response $response
     *
     * @return Response
     * @EndpointAction()
     */
    public function indexAction(Request $request, Response $response) {
        return RouteHelper::redirect($response, isset($_SESSION['auth']) ? 'backend_dashboard' : 'backend_login');
    }

}
