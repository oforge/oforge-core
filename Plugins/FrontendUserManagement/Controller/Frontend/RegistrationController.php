<?php

namespace FrontendUserManagement\Controller\Frontend;

use FrontendUserManagement\Services\RegistrationService;
use Oforge\Engine\Modules\Auth\Services\AuthService;
use Oforge\Engine\Modules\Auth\Services\PasswordService;
use Oforge\Engine\Modules\Core\Abstracts\AbstractController;
use Oforge\Engine\Modules\Session\Services\SessionManagementService;
use Slim\Http\Request;
use Slim\Http\Response;
use Slim\Router;

class RegistrationController extends AbstractController {
    public function indexAction(Request $request, Response $response) {
    }

    /**
     * Register the user and send the user to the profile page
     *
     * @param Request $request
     * @param Response $response
     *
     * @return Response
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     * @throws \Oforge\Engine\Modules\Core\Exceptions\ServiceNotFoundException
     */
    public function processAction(Request $request, Response $response) {
        /** @var AuthService $authService */
        /** @var PasswordService $passwordService */
        /** @var RegistrationService $registrationService */
        /** @var SessionManagementService $sessionManagementService*/
        /** @var Router $router */
        $authService                = null;
        $passwordService            = null;
        $registrationService        = null;
        $sessionManagementService   = null;
        $router                     = null;
        $uri                        = null;
        $body                       = null;
        $jwt                        = null;
        $email                      = null;
        $password                   = null;
        $passwordConfirm            = null;
        $user                       = null;

        if (empty($_SESSION)) {
            print_r("No session :/");
            die();
        }

        $registrationService    = Oforge()->Services()->get('frontend.user.management.registration');
        $router                 = Oforge()->App()->getContainer()->get("router");
        $uri                    = $router->pathFor("frontend_registration");

        /**
         * disallow direct processAction call. Only post action is allowed
         */
        if (!$request->isPost()) {
            return $response->withRedirect($uri, 302);
        }

        $body               = $request->getParsedBody();
        $email              = $body['frontend_registration_email'];
        $password           = $body['frontend_registration_password'];
        $passwordConfirm    = $body['frontend_registration_password_confirm'];

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
        if (!$email || !$password || !$passwordConfirm) {
            return $response->withRedirect($uri, 302);
        }

        /**
         * Password and password confirmation are not equal
         */
        if ($password !== $passwordConfirm) {
            return $response->withRedirect($uri, 302);
        }

        $passwordService    = Oforge()->Services()->get('password');
        $authService        = Oforge()->Services()->get('auth');
        $password           = $passwordService->hash($password);
        $user               = $registrationService->register($email, $password);

        /**
         * Registration failed
         * Maybe someone tried to register with an email address that is already in use
         * TODO: respond to the registration process with a nice information?
         */
        if (!$user) {
            return $response->withRedirect($uri, 302);
        }

        $jwt = $authService->createJWT($user);

        /**
         * $jwt is null if the login credentials are incorrect
         */
        if (!isset($jwt)) {
            return $response->withRedirect($uri, 302);
        }

        $sessionManagementService = Oforge()->Services()->get('session.management');
        $sessionManagementService->regenerateSession();

        $_SESSION['auth'] = $jwt;

        $uri = $router->pathFor("frontend_profile");

        /**
         * TODO: Send Email with registration information
         */

        return $response->withRedirect($uri, 302);
    }
}