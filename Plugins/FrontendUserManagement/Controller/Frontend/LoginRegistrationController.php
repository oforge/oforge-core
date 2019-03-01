<?php

namespace FrontendUserManagement\Controller\Frontend;

use Oforge\Engine\Modules\Core\Abstracts\AbstractController;
use Oforge\Engine\Modules\Core\Services\RedirectService;
use Slim\Http\Request;
use Slim\Http\Response;

class LoginRegistrationController extends AbstractController {
    public function indexAction(Request $request, Response $response) {
        /** @var RedirectService $redirectService */
        $redirectService = Oforge()->Services()->get('redirect');
        $redirectService->setRedirectUrlName('frontend_login_registration');
    }
}
