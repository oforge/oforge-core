<?php

namespace FrontendUserManagement\Controller\Frontend;

use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use FrontendUserManagement\Services\RegistrationService;
use Oforge\Engine\Modules\Auth\Services\AuthService;
use Oforge\Engine\Modules\Auth\Services\PasswordService;
use Oforge\Engine\Modules\Core\Abstracts\AbstractController;
use Oforge\Engine\Modules\Core\Exceptions\ConfigElementNotFoundException;
use Oforge\Engine\Modules\Core\Exceptions\ConfigOptionKeyNotExists;
use Oforge\Engine\Modules\Core\Exceptions\ServiceNotFoundException;
use Oforge\Engine\Modules\Core\Services\RedirectService;
use Oforge\Engine\Modules\I18n\Helper\I18N;
use Oforge\Engine\Modules\Mailer\Services\MailService;
use Oforge\Engine\Modules\Session\Services\SessionManagementService;
use PHPMailer\PHPMailer\Exception;
use Slim\Http\Request;
use Slim\Http\Response;
use Slim\Router;

/**
 * Class RegistrationController
 *
 * @package FrontendUserManagement\Controller\Frontend
 */
class RegistrationController extends AbstractController {

    /**
     * @param Request $request
     * @param Response $response
     *
     * @throws ServiceNotFoundException
     */
    public function indexAction(Request $request, Response $response) {
        /** @var RedirectService $redirectService */
        $redirectService = Oforge()->Services()->get('redirect');
        $redirectService->setRedirectUrlName('frontend_registration');
    }

    /**
     * @param Request $request
     * @param Response $response
     *
     * @return Response
     * @throws ORMException
     * @throws OptimisticLockException
     * @throws ConfigElementNotFoundException
     * @throws ConfigOptionKeyNotExists
     * @throws ServiceNotFoundException
     * @throws Exception
     */
    public function processAction(Request $request, Response $response) {
        /** @var PasswordService $passwordService */ /** @var RegistrationService $registrationService */ /** @var SessionManagementService $sessionManagementService */ /** @var Router $router */
        /** @var MailService $mailService */
        $passwordService          = null;
        $registrationService      = null;
        $sessionManagementService = null;
        $router                   = null;
        $uri                      = null;
        $body                     = null;
        $jwt                      = null;
        $email                    = null;
        $password                 = null;
        $passwordConfirm          = null;
        $user                     = null;
        $privacyNoticeAccepted    = null;
        $activationLink           = null;
        $mailService              = null;

        if (empty($_SESSION)) {
            print_r('No session :/');
            die();
        }

        $registrationService = Oforge()->Services()->get('frontend.user.management.registration');
        $router              = Oforge()->App()->getContainer()->get('router');

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
            Oforge()->View()->Flash()->addMessage('warning', I18N::translate('invalid_direct_page_call', 'Direct page call is not allowed.'));

            return $response->withRedirect($uri, 302);
        }

        $body                  = $request->getParsedBody();
        $email                 = $body['frontend_registration_email'];
        $password              = $body['frontend_registration_password'];
        $passwordConfirm       = $body['frontend_registration_password_confirm'];
        $privacyNoticeAccepted = $body['frontend_registration_privacy_notice_accepted'];

        /**
         * no token was sent
         */
        if (!isset($body['token']) || empty($body['token'])) {
            Oforge()->View()->Flash()->addMessage('warning', I18N::translate('form_invalid_token', 'The data has been sent from an invalid form.'));
            Oforge()->Logger()->get()->addWarning('Someone tried to do a backend login with a form without csrf token! Redirecting to backend login.');

            return $response->withRedirect($uri, 302);
        }

        /**
         * invalid token was sent
         */
        if (!hash_equals($_SESSION['token'], $body['token'])) {
            Oforge()->View()->Flash()->addMessage('warning', I18N::translate('form_invalid_token', 'The data has been sent from an invalid form.'));
            Oforge()->Logger()->get()->addWarning('Someone tried a backend login without a valid form csrf token! Redirecting back to login.');

            return $response->withRedirect($uri, 302);
        }

        /**
         * no email or password body was sent
         */
        if (!$email || !$password || !$passwordConfirm || !$privacyNoticeAccepted) {
            Oforge()->View()->Flash()->addMessage('warning', I18N::translate('form_invalid_data', 'Invalid form data.'));

            return $response->withRedirect($uri, 302);
        }

        /**
         * Password and password confirmation are not equal
         */
        if ($password !== $passwordConfirm) {
            Oforge()->View()->Flash()->addMessage('warning', I18N::translate('form_password_mismatch', 'Passwords do not match.'));

            return $response->withRedirect($uri, 302);
        }

        $passwordService = Oforge()->Services()->get('password');
        $password        = $passwordService->hash($password);
        $user            = $registrationService->register($email, $password);

        /**
         * Registration failed
         * Maybe someone tried to register with an email address that is already in use
         * TODO: respond to the registration process with a nice information?
         */
        if (!$user) {
            Oforge()->View()->Flash()->addMessage('warning', I18N::translate('registration_failed', 'Registration failed.'));

            return $response->withRedirect($uri, 302);
        }

        /**
         * create activation link
         */
        $activationLink = $registrationService->generateActivationLink($user);

        $mailService = Oforge()->Services()->get('mail');

        // TODO: add email snippets
        $mailOptions = [
            'to'      => [$user['email'] => $user['email']],
            'subject' => 'Oforge | Your registration!',
            'template' => 'RegisterConfirmation.twig',
        ];
        $templateData = [
            'activationLink' => $activationLink,
        ];

        $mailService->send($mailOptions ,$templateData);

        $uri = $router->pathFor('frontend_login');
        Oforge()->View()->Flash()->addMessage('success', I18N::translate('registration_success_mail_send',
            'Registration successful. You will receive an email with information about you account activation.'));

        return $response->withRedirect($uri, 302);
    }

    /**
     * @param Request $request
     * @param Response $response
     *
     * @return Response
     * @throws ORMException
     * @throws ServiceNotFoundException
     */
    public function activateAction(Request $request, Response $response) {
        /** @var SessionManagementService $sessionManagementService */ /** @var RegistrationService $registrationService */
        /** @var AuthService $authService */
        $sessionManagementService = Oforge()->Services()->get('session.management');
        $registrationService      = Oforge()->Services()->get('frontend.user.management.registration');
        $authService              = Oforge()->Services()->get('auth');
        $guid                     = $request->getParam('activate');
        $router                   = Oforge()->App()->getContainer()->get('router');
        $uri                      = $router->pathFor('frontend_registration');
        $jwt                      = null;
        $user                     = null;

        /**
         * if there is no guid
         */
        if (!$guid) {
            return $response->withRedirect($uri, 302);
        }

        $user = $registrationService->activate($guid);

        /**
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

        $uri = $router->pathFor('frontend_account_dashboard');
        Oforge()->View()->Flash()
                ->addMessage('success', I18N::translate('registration_success_logined', 'Your account was activated successfully. You are now logged in.'));

        return $response->withRedirect($uri, 302);
    }

}
