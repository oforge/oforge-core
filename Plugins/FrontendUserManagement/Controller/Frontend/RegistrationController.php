<?php

namespace FrontendUserManagement\Controller\Frontend;

use FrontendUserManagement\Services\RegistrationService;
use Oforge\Engine\Modules\Core\Abstracts\AbstractController;
use Oforge\Engine\Modules\Session\Services\SessionManagementService;
use Slim\Http\Request;
use Slim\Http\Response;
use Slim\Router;

class RegistrationController extends AbstractController {
    public function indexAction(Request $request, Response $response) {
    }

    /**
     * @param Request $request
     * @param Response $response
     *
     * @return Response
     * @throws \Oforge\Engine\Modules\Core\Exceptions\ServiceNotFoundException
     */
    public function processAction(Request $request, Response $response) {
        if (empty($_SESSION)) {
            print_r("No session :/");
            die();
        }

        /** @var RegistrationService $registrationService */
        $registrationService = Oforge()->Services()->get('frontend.user.management.registration');

        /**
         * @var $router Router
         */
        $router = Oforge()->App()->getContainer()->get("router");
        $uri = $router->pathFor("frontend_registration");

        /**
         * disallow direct processAction call. Only post action is allowed
         */
        if (!$request->isPost()) {
            return $response->withRedirect($uri, 302);
        }

        $body = $request->getParsedBody();
        $jwt = null;

        /**
         * no token was sent
         */
        if (!isset($body['token']) || empty($body['token'])) {
            Oforge()->Logger()->get()->addWarning("Someone tried to do a backend login with a form without csrf token! Redirecting to backend login.");

            return $response->withRedirect($uri, 302);
        }

        /**
         * invalid token was sent
         */
        if (!hash_equals($_SESSION['token'], $body['token'])) {
            Oforge()->Logger()->get()->addWarning("Someone tried a backend login without a valid form csrf token! Redirecting back to login.");
            return $response->withRedirect($uri, 302);
        }

        /**
         * no email or password body was sent
         */
        if (!array_key_exists("frontend_registration_email", $body) ||
            !array_key_exists("frontend_registration_password", $body) ||
            !array_key_exists("frontend_registration_password_confirm", $body)) {
            return $response->withRedirect($uri, 302);
        }

        /**
         * Password and password confirmation are not equal
         */
        if ($body['frontend_registration_password'] !== $body['frontend_registration_password_confirm']) {
            return $response->withRedirect($uri, 302);
        }

        /**
         * TODO: - implement registration
         *       - registration creates new jwt to store in the session.
         */
        $jwt = $registrationService->register();

        /**
         * $jwt is null if the login credentials are incorrect
         */
        if (!isset($jwt)) {
            return $response->withRedirect($uri, 302);
        }

        /**
         * @var $sessionManagement SessionManagementService
         */
        $sessionManagement = Oforge()->Services()->get('session.management');
        $sessionManagement->regenerateSession();

        $_SESSION['auth'] = $jwt;

        $uri = $router->pathFor("frontend_profile");

        return $response->withRedirect($uri, 302);
    }
}