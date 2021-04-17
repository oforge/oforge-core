<?php

namespace FrontendUserManagement\Controller\Frontend;

use Exception;
use Oforge\Engine\Modules\Core\Annotation\Endpoint\EndpointAction;
use Oforge\Engine\Modules\Core\Annotation\Endpoint\EndpointClass;
use Oforge\Engine\Modules\Core\Helper\RouteHelper;
use Oforge\Engine\Modules\Core\Manager\SessionManager;
use Oforge\Engine\Modules\I18n\Helper\I18N;
use Slim\Http\Request;
use Slim\Http\Response;

/**
 * Class LogoutController
 *
 * @package FrontendUserManagement\Controller\Frontend
 * @EndpointClass(path="/logout", name="frontend_logout", assetScope="Frontend")
 */
class LogoutController
{

    /**
     * @param Request $request
     * @param Response $response
     *
     * @return Response
     * @throws Exception
     * @EndpointAction()
     */
    public function indexAction(Request $request, Response $response)
    {
        SessionManager::destroy();
        SessionManager::start();
        Oforge()->View()->Flash()->addMessage('success', I18N::translate('logout_success', 'You have been logged out.'));

        return RouteHelper::redirect($response, 'frontend_login');
    }

}
