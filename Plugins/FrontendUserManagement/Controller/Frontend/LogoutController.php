<?php

namespace FrontendUserManagement\Controller\Frontend;

use Exception;
use Oforge\Engine\Modules\Core\Annotation\Endpoint\EndpointAction;
use Oforge\Engine\Modules\Core\Annotation\Endpoint\EndpointClass;
use Oforge\Engine\Modules\Core\Services\Session\SessionManagementService;
use Oforge\Engine\Modules\I18n\Helper\I18N;
use Slim\Http\Request;
use Slim\Http\Response;
use Slim\Router;

/**
 * Class LogoutController
 *
 * @package FrontendUserManagement\Controller\Frontend
 * @EndpointClass(path="/logout", name="frontend_logout", assetScope="Frontend")
 */
class LogoutController {

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
        /** @var Router $router */
        $sessionManager = Oforge()->Services()->get('session.management');
        $router         = Oforge()->App()->getContainer()->get('router');
        $sessionManager->sessionDestroy();
        $sessionManager->sessionStart();
        Oforge()->View()->Flash()->addMessage('success', I18N::translate('logout_success', 'You have been logged out.'));

        return $response->withRedirect($router->pathFor('frontend_login'), 302);
    }

}
