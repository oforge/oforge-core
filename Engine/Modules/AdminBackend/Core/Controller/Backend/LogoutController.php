<?php

namespace Oforge\Engine\Modules\AdminBackend\Core\Controller\Backend;

use Oforge\Engine\Modules\AdminBackend\Core\Abstracts\SecureBackendController;
use Oforge\Engine\Modules\Auth\Models\User\BackendUser;
use Oforge\Engine\Modules\Session\Services\SessionManagementService;
use Slim\Http\Request;
use Slim\Http\Response;
use Slim\Router;

class LogoutController extends SecureBackendController
{
    /**
     * @param Request $request
     * @param Response $response
     *
     * @return Response
     * @throws \Exception
     */
    public function indexAction(Request $request, Response $response) {
        /**
         * @var $sessionManager SessionManagementService
         */
        $sessionManager = Oforge()->Services()->get("session.management");
        $sessionManager->sessionDestroy();
        /**
         * @var $router Router
         */
        $router = Oforge()->App()->getContainer()->get("router");
        return $response->withRedirect($router->pathFor("backend_login"), 302);
    }
    
    public function initPermissions() {
        $this->ensurePermissions("indexAction", BackendUser::class, BackendUser::ROLE_MODERATOR);
    }
}
