<?php

namespace FrontendUserManagement\Controller\Frontend;

use Oforge\Engine\Modules\Session\Services\SessionManagementService;
use Slim\Http\Request;
use Slim\Http\Response;
use Slim\Router;

class LogoutController {
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
        $sessionManager = Oforge()->Services()->get('session.management');
        $sessionManager->sessionDestroy();
        $sessionManager->sessionStart();
        /**
         * @var $router Router
         */
        $router = Oforge()->App()->getContainer()->get('router');
        Oforge()->View()->addFlashMessage('success', 'You have been logged out.');
        return $response->withRedirect($router->pathFor('frontend_login'), 302);
    }
}
