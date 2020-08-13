<?php

namespace FrontendUserManagement\Controller\Frontend;

use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use FrontendUserManagement\Models\User;
use FrontendUserManagement\Services\RegistrationService;
use FrontendUserManagement\Services\UserDetailsService;
use Oforge\Engine\Modules\Auth\Enums\InvalidPasswordFormatException;
use Oforge\Engine\Modules\Auth\Services\AuthService;
use Oforge\Engine\Modules\Auth\Services\PasswordService;
use Oforge\Engine\Modules\Core\Abstracts\AbstractController;
use Oforge\Engine\Modules\Core\Annotation\Endpoint\EndpointAction;
use Oforge\Engine\Modules\Core\Annotation\Endpoint\EndpointClass;
use Oforge\Engine\Modules\Core\Exceptions\ConfigElementNotFoundException;
use Oforge\Engine\Modules\Core\Exceptions\ConfigOptionKeyNotExistException;
use Oforge\Engine\Modules\Core\Exceptions\ServiceNotFoundException;
use Oforge\Engine\Modules\Core\Helper\ArrayHelper;
use Oforge\Engine\Modules\Core\Helper\RouteHelper;
use Oforge\Engine\Modules\Core\Services\RedirectService;
use Oforge\Engine\Modules\Core\Services\Session\SessionManagementService;
use Oforge\Engine\Modules\Core\Services\TokenService;
use Oforge\Engine\Modules\I18n\Helper\I18N;
use Oforge\Engine\Modules\Mailer\Services\MailService;
use Slim\Http\Request;
use Slim\Http\Response;
use Slim\Router;
use Twig_Error_Loader;
use Twig_Error_Runtime;
use Twig_Error_Syntax;

/**
 * Class RegistrationController
 *
 * @package FrontendUserManagement\Controller\Frontend
 * @EndpointClass(path="/registration", name="frontend_registration", assetBundles="Frontend")
 */
class RegistrationController extends AbstractController {

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
        $redirectService->setRedirectUrlName('frontend_registration');
    }

    /**
     * @param Request $request
     * @param Response $response
     *
     * @return Response
     * @throws ConfigElementNotFoundException
     * @throws ConfigOptionKeyNotExistException
     * @throws ORMException
     * @throws OptimisticLockException
     * @throws ServiceNotFoundException
     * @throws Twig_Error_Loader
     * @throws Twig_Error_Runtime
     * @throws Twig_Error_Syntax
     * @EndpointAction()
     */
    public function processAction(Request $request, Response $response) {
        /**
         * @var PasswordService $passwordService
         * @var RegistrationService $registrationService
         * @var SessionManagementService $sessionManagementService
         * @var Router $router
         * @var MailService $mailService
         */
        $passwordService          = null;
        $registrationService      = null;
        $sessionManagementService = null;
        $router                   = null;
        $uri                      = null;
        $body                     = null;
        $jwt                      = null;
        $email                    = null;
        $nickname                 = null;
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
        /** @var RegistrationService $registrationService */
        $registrationService = Oforge()->Services()->get('frontend.user.management.registration');
        /** @var UserDetailsService $userDetailService */
        $userDetailService = Oforge()->Services()->get('frontend.user.management.user.details');
        /** @var Router $router */
        $router = Oforge()->App()->getContainer()->get('router');

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
            Oforge()->View()->Flash()->addMessage('warning', I18N::translate('invalid_direct_page_call', [
                'en' => 'Direct page call is not allowed.',
                'de' => 'Unbefugter Seitenaufruf.',
            ]));

            return $response->withRedirect($uri, 302);
        }

        $body                  = $request->getParsedBody();
        $email                 = $body['frontend_registration_email'];
        $password              = $body['frontend_registration_password'];
        $passwordConfirm       = $body['frontend_registration_password_confirm'];
        $privacyNoticeAccepted = $body['frontend_registration_privacy_notice_accepted'];
        $referrer              = ArrayHelper::get($body, 'frontend_registration_referrer');
        $forceReferrer         = ArrayHelper::get($body, 'frontend_registration_referrer_force');

        if (!isset($body['frontend_registration_nickname']) || empty($body['frontend_registration_nickname'])) {
            $nickname = $userDetailService->generateNickname();
        } else {
            $nickname = $body['frontend_registration_nickname'];
        }

        if (isset($referrer)) {
            $uri = $referrer;

            if ($forceReferrer) {
                $_SESSION['force_referrer'] = $uri;
            }
        }

        /**
         * no token was sent
         */
        if (!isset($body['token']) || empty($body['token'])) {
            Oforge()->View()->Flash()->addMessage('warning', I18N::translate('form_invalid_token', [
                'en' => 'The data has been sent from an invalid form.',
                'de' => 'Ungüliger Token.',
            ]));
            Oforge()->Logger()->get()->addWarning('Someone tried to do a backend login with a form without csrf token! Redirecting to backend login.');

            return $response->withRedirect($uri, 302);
        }

        /**
         * invalid token was sent
         */
        /** @var TokenService $tokenService */
        $tokenService = Oforge()->Services()->get('token');
        if (!$tokenService->isValid($body['token'])) {
            Oforge()->View()->Flash()->addMessage('warning', I18N::translate('form_invalid_token', [
                'en' => 'The data has been sent from an invalid form.',
                'de' => 'Ungültiger Token.',
            ]));
            Oforge()->Logger()->get()->addWarning('Someone tried a backend login without a valid form csrf token! Redirecting back to login.');

            return $response->withRedirect($uri, 302);
        }

        /**
         * no email or password body was sent
         */
        if (!$email || !$password || !$passwordConfirm || !$privacyNoticeAccepted) {
            Oforge()->View()->Flash()->addMessage('warning', I18N::translate('form_invalid_data', [
                'en' => 'Invalid form data.',
                'de' => 'Ungültiges Formular.',
            ]));

            return $response->withRedirect($uri, 302);
        }

        /**
         * Password and password confirmation are not equal
         */
        if ($password !== $passwordConfirm) {
            Oforge()->View()->Flash()->addMessage('warning', I18N::translate('form_password_mismatch', [
                'en' => 'Passwords do not match.',
                'de' => 'Passwörter stimmen nicht überein.',
            ]));

            return $response->withRedirect($uri, 302);
        }

        try {
            /** @var PasswordService $passwordService */
            $passwordService = Oforge()->Services()->get('password');
            $password        = $passwordService->validateFormat($password)->hash($password);
        } catch (InvalidPasswordFormatException $exception) {
            Oforge()->View()->Flash()->addMessage('error', $exception->getMessage());

            return $response->withRedirect($uri, 302);
        }
        $user = $registrationService->register($email, $password);

        /**
         * Registration failed
         * Maybe someone tried to register with an email address that is already in use
         */
        if (!$user) {
            Oforge()->View()->Flash()->addMessage('warning', I18N::translate('registration_user_already_exists', [
                'en' => 'There already exists an account with this mail',
                'de' => 'Es existiert ein Account mit dieser E-Mail.',
            ]));

            return $response->withRedirect($uri, 302);

        }

        $_SESSION['registration_success'] = true;
        $_SESSION['user_id']              = $user['id'];

        $userDetailService->save(['userId' => $user['id'], 'nickname' => $nickname]);

        /**
         * create activation link
         */
        $activationLink = $registrationService->generateActivationLink($user);

        $mailService = Oforge()->Services()->get('mail');

        $mailOptions  = [
            'to'       => [$user['email'] => $user['email']],
            'from'     => 'info',
            'subject'  => I18N::translate('mailer_subject_registration', [
                'en' => 'Your Registration',
                'de' => 'Deine Registrierung',
            ]),
            'template' => 'RegisterConfirm.twig',
        ];
        $templateData = [
            'activationLink' => $activationLink,
            'resendLink'     => RouteHelper::getFullUrl(RouteHelper::getUrl('frontend_registration_resend_activation_link')),
            'user_mail'      => $user['email'],
            'sender_mail'    => $mailService->getSenderAddress('info'),
            'receiver_name'  => $nickname,
        ];

        /**
         * Registration Mail could not be sent
         */
        if (!$mailService->send($mailOptions, $templateData)) {
            Oforge()->View()->Flash()->addMessage('error', I18N::translate('registration_mail_error', [
                'en' => 'Your registration mail could not be sent',
                'de' => 'Registrierungs-Mail konnte nicht gesendet werden.',

            ]));
            $registrationService->unregister($user);

            return $response->withRedirect($uri, 302);
        }

        if (!isset($referrer)) {
            $uri = $router->pathFor('frontend_login');
        }
        Oforge()->View()->Flash()->addMessage('success', I18N::translate('registration_mail_success', [
            'en' => 'Registration successful. You will receive an email with information about you account activation.',
            'de' => 'Registrierung erfolgreich. In Kürze erhälst du eine E-Mail mit Informationen zur Account-Aktivierung.',
        ]));

        return $response->withRedirect($uri, 302);
    }

    /**
     * @param Request $request
     * @param Response $response
     *
     * @return Response
     * @throws ConfigElementNotFoundException
     * @throws ConfigOptionKeyNotExistException
     * @throws ORMException
     * @throws ServiceNotFoundException
     * @throws Twig_Error_Loader
     * @throws Twig_Error_Runtime
     * @throws Twig_Error_Syntax
     * @EndpointAction()
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
        /**
         * check if user is already activated
         */
        $is_active = $registrationService->userIsActive($guid);
        if ($is_active) {
            Oforge()->View()->Flash()->addMessage('warning', I18N::translate('frontend_user_already_active', [
                'en' => 'Your account has already been activated and you are able to log in.',
                'de' => 'Dein Account wurde schon aktiviert und du kannst dich einloggen.',
            ]));

            return $response->withRedirect($router->pathFor('frontend_login'), 302);
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
        Oforge()->View()->Flash()->addMessage('success', I18N::translate('registration_success_logined', [
            'en' => 'Your account was activated successfully. You are now logged in.',
            'de' => 'Dein Account wurde erfolgreich aktiviert. Du bist nun angemeldet. ',
        ]), true, "registration--successful");

        Oforge()->View()->Flash()->setData("new_registration", ['newRegistration' => true]);

        /**
         * Send Welcome Mail (requires RegistrationGiftCertificate.twig)
         */

        /** @var MailService $mailService */
        $mailService = Oforge()->Services()->get('mail');

        $options = [
            'to'       => [$user['email'] => $user['email']],
            'from'     => 'info',
            'subject'  => I18N::translate('mailer_subject_registration_gift_cert'),
            'template' => 'RegistrationGiftCertificate.twig',
        ];

        $mailService->send($options);

        if (isset($_SESSION['force_referrer'])) {
            $uri = $_SESSION['force_referrer'];
            unset($_SESSION['force_referrer']);
        }

        return $response->withRedirect($uri, 302);
    }

    /**
     * @param Request $request
     * @param Response $response
     * @EndpointAction(path="resend-activation-link", name="resend_activation_link")
     */
    public function resendActivationLinkAction(Request $request, Response $response) {
        if ($request->isPost() && !empty($postData = $request->getParsedBody()) && is_array($postData)) {
            // if (empty($_SESSION)) {
            //     print_r('No session :/');
            //     die();
            // }
            $email = ArrayHelper::get($postData, 'email');

            /** @var RegistrationService $registrationService */
            $registrationService = Oforge()->Services()->get('frontend.user.management.registration');
            if (!isset($postData['token']) || empty($postData['token'])) {
                Oforge()->View()->Flash()->addMessage('warning', I18N::translate('form_invalid_token', [
                    'en' => 'The data has been sent from an invalid form.',
                    'de' => 'Ungüliger Token.',
                ]));

                return RouteHelper::redirect($response, 'frontend_registration_resend_activation_link');
            }
            /** @var TokenService $tokenService */
            $tokenService = Oforge()->Services()->get('token');
            if (!$tokenService->isValid($postData['token'])) {
                // invalid token was sent
                Oforge()->View()->Flash()->addMessage('warning', I18N::translate('form_invalid_token', [
                    'en' => 'The data has been sent from an invalid form.',
                    'de' => 'Ungültiger Token.',
                ]));

                return RouteHelper::redirect($response, 'frontend_registration_resend_activation_link');
            }
            if (empty($email)) {
                Oforge()->View()->Flash()->addMessage('warning', I18N::translate('form_invalid_data', [
                    'en' => 'Invalid form data.',
                    'de' => 'Ungültiges Formular.',
                ]));

                return RouteHelper::redirect($response, 'frontend_registration_resend_activation_link');
            }

            /** @var User|null $user */
            $user = $registrationService->getUser($email);
            if ($user === null) {
                return RouteHelper::redirect($response, 'frontend_registration_resend_activation_link');
            }
            $userData                         = $user->toArray();
            $_SESSION['registration_success'] = true;
            $_SESSION['user_id']              = $userData['id'];
            // create activation link
            $activationLink = $registrationService->generateActivationLink($userData);

            /** @var MailService $mailService */
            $mailService  = Oforge()->Services()->get('mail');
            $mailOptions  = [
                'to'       => [$userData['email'] => $userData['email']],
                'from'     => 'info',
                'subject'  => I18N::translate('mailer_subject_registration', [
                    'en' => 'Your Registration',
                    'de' => 'Deine Registrierung',
                ]),
                'template' => 'RegisterConfirm.twig',
            ];
            $templateData = [
                'activationLink' => $activationLink,
                'resendLink'     => RouteHelper::getFullUrl(RouteHelper::getUrl('frontend_registration_resend_activation_link')),
                'user_mail'      => $userData['email'],
                'sender_mail'    => $mailService->getSenderAddress('info'),
                'receiver_name'  => ArrayHelper::dotGet($userData, 'detail.nickName') ?? $userData['email'] ?? '',
            ];
            // Registration Mail could not be sent
            if (!$mailService->send($mailOptions, $templateData)) {
                Oforge()->View()->Flash()->addMessage('error', I18N::translate('registration_mail_error', [
                    'en' => 'Your registration mail could not be sent',
                    'de' => 'Registrierungs-Mail konnte nicht gesendet werden.',

                ]));

                return RouteHelper::redirect($response, 'frontend_registration_resend_activation_link');
            }
            Oforge()->View()->Flash()->addMessage('success', I18N::translate('resend_registration_activation_link_mail_success', [
                'en' => 'You will receive an email with information about you account activation.',
                'de' => 'In Kürze erhälst du eine E-Mail mit Informationen zur Account-Aktivierung.',
            ]));

            return RouteHelper::redirect($response, 'frontend_login');
        }

        return $response;
    }

}
