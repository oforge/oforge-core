<?php

namespace FrontendUserManagement\Controller\Frontend;

use Oforge\Engine\Modules\Core\Abstracts\AbstractController;
use Oforge\Engine\Modules\Core\Annotation\Endpoint\EndpointAction;
use Oforge\Engine\Modules\Core\Annotation\Endpoint\EndpointClass;
use Oforge\Engine\Modules\Core\Exceptions\ServiceNotFoundException;
use Oforge\Engine\Modules\Core\Services\RedirectService;
use Slim\Http\Request;
use Slim\Http\Response;

/**
 * Class LoginRegistrationController
 *
 * @package FrontendUserManagement\Controller\Frontend
 * @EndpointClass(path="/login-registration", name="frontend_login_registration", assetScope="Frontend")
 */
class LoginRegistrationController extends AbstractController {

    /**
     * @param Request $request
     * @param Response $response
     *
     * @throws ServiceNotFoundException
     * @EndpointAction()
     */
    public function indexAction(Request $request, Response $response) {
        /** @var RedirectService $redirectService */
        $redirectService = Oforge()->Services()->get('redirect');
        $redirectService->setRedirectUrlName('frontend_login_registration');
    }

}
