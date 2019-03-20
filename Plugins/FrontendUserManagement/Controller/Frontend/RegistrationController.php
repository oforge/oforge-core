<?php

namespace FrontendUserManagement\Controller\Frontend;

use FrontendUserManagement\Services\RegistrationService;
use Oforge\Engine\Modules\Auth\Services\AuthService;
use Oforge\Engine\Modules\Auth\Services\PasswordService;
use Oforge\Engine\Modules\Core\Abstracts\AbstractController;
use Oforge\Engine\Modules\Core\Services\RedirectService;
use Oforge\Engine\Modules\Mailer\Services\MailService;
use Oforge\Engine\Modules\Session\Services\SessionManagementService;
use Slim\Http\Request;
use Slim\Http\Response;
use Slim\Router;

class RegistrationController extends AbstractController {
    public function indexAction(Request $request, Response $response) {
        /** @var RedirectService $redirectService */
        $redirectService = Oforge()->Services()->get('redirect');
        $redirectService->setRedirectUrlName('frontend_registration');
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
     * @throws \Oforge\Engine\Modules\Core\Exceptions\ConfigElementNotFoundException
     * @throws \Oforge\Engine\Modules\Core\Exceptions\ConfigOptionKeyNotExists
     * @throws \Oforge\Engine\Modules\Core\Exceptions\ServiceNotFoundException
     * @throws \PHPMailer\PHPMailer\Exception
     */
    public function processAction(Request $request, Response $response) {
        /** @var PasswordService $passwordService */
        /** @var RegistrationService $registrationService */
        /** @var SessionManagementService $sessionManagementService*/
        /** @var Router $router */
        /** @var MailService $mailService */
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
        $privacyNoticeAccepted      = null;
        $activationLink             = null;
        $mailService                = null;

        if (empty($_SESSION)) {
            print_r('No session :/');
            die();
        }

        $registrationService    = Oforge()->Services()->get('frontend.user.management.registration');
        $router                 = Oforge()->App()->getContainer()->get('router');

        /** @var RedirectService $redirectService */
        $redirectService = Oforge()->Services()->get('redirect');
        $redirectUrlName = 'frontend_registration';
        if ($redirectService->hasRedirectUrlName()) {
            $redirectUrlName = $redirectService->getRedirectUrlName();
        }

        $uri = $router->pathFor($redirectUrlName);

        /**
         * disallow direct processAction call. Only post action is allowed
         */
        if (!$request->isPost()) {
            Oforge()->View()->addFlashMessage('warning', 'Direct page call is not allowed.');
            return $response->withRedirect($uri, 302);
        }

        $body                   = $request->getParsedBody();
        $email                  = $body['frontend_registration_email'];
        $password               = $body['frontend_registration_password'];
        $passwordConfirm        = $body['frontend_registration_password_confirm'];
        $privacyNoticeAccepted  = $body['frontend_registration_privacy_notice_accepted'];

        /**
         * no token was sent
         */
        if (!isset($body['token']) || empty($body['token'])) {
            Oforge()->View()->addFlashMessage('warning', 'The data has been sent from an invalid form.');
            Oforge()->Logger()->get()->addWarning('Someone tried to do a backend login with a form without csrf token! Redirecting to backend login.');

            return $response->withRedirect($uri, 302);
        }

        /**
         * invalid token was sent
         */
        if (!hash_equals($_SESSION['token'], $body['token'])) {
            Oforge()->View()->addFlashMessage('warning', 'The data has been sent from an invalid form.');
            Oforge()->Logger()->get()->addWarning('Someone tried a backend login without a valid form csrf token! Redirecting back to login.');
            return $response->withRedirect($uri, 302);
        }

        /**
         * no email or password body was sent
         */
        if (!$email || !$password || !$passwordConfirm || !$privacyNoticeAccepted) {
            Oforge()->View()->addFlashMessage('warning', 'Invalid form data.');
            return $response->withRedirect($uri, 302);
        }

        /**
         * Password and password confirmation are not equal
         */
        if ($password !== $passwordConfirm) {
            Oforge()->View()->addFlashMessage('warning', 'Passwords do not match.');
            return $response->withRedirect($uri, 302);
        }

        $passwordService    = Oforge()->Services()->get('password');
        $password           = $passwordService->hash($password);
        $user               = $registrationService->register($email, $password);

        /**
         * Registration failed
         * Maybe someone tried to register with an email address that is already in use
         * TODO: respond to the registration process with a nice information?
         */
        if (!$user) {
            Oforge()->View()->addFlashMessage('warning', 'Registration failed.');
            return $response->withRedirect($uri, 302);
        }

        /*
         * create activation link
         */
        $activationLink = $registrationService->generateActivationLink($user);

        $mailService = Oforge()->Services()->get('mail');

        // TODO: add email snippets
        $mailOptions = [
            'to' => [$user['email'] => $user['email']],
            'subject' => 'Oforge | Your registration!',
            'body' => 'You are registered and have to activate your account. Your activation link is: '.$activationLink
        ];

        $mailService->send($mailOptions);

        $uri = $router->pathFor('frontend_login');
        Oforge()->View()->addFlashMessage('success', 'Registration successful. You will receive an email with information about you account activation.');

        return $response->withRedirect($uri, 302);
    }

    /**
     * @param Request $request
     * @param Response $response
     *
     * @return Response
     * @throws \Doctrine\ORM\ORMException
     * @throws \Oforge\Engine\Modules\Core\Exceptions\ServiceNotFoundException
     */
    public function activateAction(Request $request, Response $response) {
        /** @var SessionManagementService $sessionManagementService */
        /** @var RegistrationService $registrationService */
        /** @var AuthService $authService */
        $sessionManagementService   = Oforge()->Services()->get('session.management');
        $registrationService        = Oforge()->Services()->get('frontend.user.management.registration');
        $authService                = Oforge()->Services()->get('auth');
        $guid                       = $request->getParam('activate');
        $router                     = Oforge()->App()->getContainer()->get('router');
        $uri                        = $router->pathFor('frontend_registration');
        $jwt                        = null;
        $user                       = null;

        /*
         * if there is no guid
         */
        if (!$guid) {
            return $response->withRedirect($uri, 302);
        }

        $user = $registrationService->activate($guid);

        /*
         * User not found
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

        $sessionManagementService->regenerateSession();
        $_SESSION['auth'] = $jwt;

        $uri = $router->pathFor('frontend_profile_dashboard');

        Oforge()->View()->addFlashMessage('success', 'Your account was activated successfully. You are now logged in.');

        return $response->withRedirect($uri, 302);
    }
}