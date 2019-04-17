<?php

namespace FrontendUserManagement\Controller\Frontend;

use Oforge\Engine\Modules\Core\Services\Session\SessionManagementService;
use Oforge\Engine\Modules\I18n\Helper\I18N;
use Slim\Http\Request;
use Slim\Http\Response;
use Slim\Router;

/**
 * Class LogoutController
 *
 * @package FrontendUserManagement\Controller\Frontend
 */
class LogoutController {

    /**
     * @param Request $request
     * @param Response $response
     *
     * @return Response
     * @throws \Exception
     */
    public function indexAction(Request $request, Response $response) {
        /** @var SessionManagementService $sessionManager */
        $sessionManager = Oforge()->Services()->get('session.management');
        $sessionManager->sessionDestroy();
        $sessionManager->sessionStart();
        /** @var Router $router */
        $router = Oforge()->App()->getContainer()->get('router');
        Oforge()->View()->Flash()->addMessage('success', I18N::translate('logout_success', 'You have been logged out.'));

        return $response->withRedirect($router->pathFor('frontend_login'), 302);
    }

}
