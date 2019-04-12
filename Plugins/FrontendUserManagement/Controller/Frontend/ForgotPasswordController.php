<?php
/**
 * Created by PhpStorm.
 * User: Alexander Wegner
 * Date: 11.02.2019
 * Time: 10:05
 */

namespace FrontendUserManagement\Controller\Frontend;

use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use FrontendUserManagement\Services\PasswordResetService;
use Interop\Container\Exception\ContainerException;
use Oforge\Engine\Modules\Auth\Services\AuthService;
use Oforge\Engine\Modules\Auth\Services\PasswordService;
use Oforge\Engine\Modules\Core\Abstracts\AbstractController;
use Oforge\Engine\Modules\Core\Exceptions\ServiceNotFoundException;
use Oforge\Engine\Modules\I18n\Helper\I18N;
use Oforge\Engine\Modules\Session\Services\SessionManagementService;
use Slim\Http\Request;
use Slim\Http\Response;
use Slim\Router;

/**
 * Class ForgotPasswordController
 *
 * @package FrontendUserManagement\Controller\Frontend
 */
class ForgotPasswordController extends AbstractController {

    /**
     * @param Request $request
     * @param Response $response
     */
    public function indexAction(Request $request, Response $response) {
        // show the email form for requesting a reset link
    }

    /**
     * @param Request $request
     * @param Response $response
     *
     * @return Response
     * @throws ORMException
     * @throws OptimisticLockException
     * @throws ContainerException
     * @throws ServiceNotFoundException
     */
    public function processAction(Request $request, Response $response) {
        /** @var PasswordResetService $passwordResetService */
        /** @var Router $router */
        $passwordResetService = Oforge()->Services()->get('password.reset');
        $router               = Oforge()->Container()->get('router');
        $body                 = $request->getParsedBody();
        $email                = $body['forgot-password__email'];
        $token                = $body['token'];
        $uri                  = $router->pathFor('frontend_forgot_password');

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
         * no email body was sent
         */
        if (!$email) {
            Oforge()->View()->Flash()->addMessage('warning', I18N::translate('form_invalid_data', 'Invalid form data.'));

            return $response->withRedirect($uri, 302);
        }

        /**
         * Email not found
         */
        if (!$passwordResetService->emailExists($email)) {
            Oforge()->View()->Flash()->addMessage('warning', I18N::translate('user_mail_missing', 'Email not found.'));

            return $response->withRedirect($uri, 302);
        }

        $passwordResetLink = $passwordResetService->createPasswordResetLink($email);

        $mailService = Oforge()->Services()->get('mail');

        // TODO: add email snippets
        $mailOptions = [
            'to'      => [$email => $email],
            'subject' => 'Oforge | Your password reset!',
            'template' => 'ResetPassword.twig',
        ];
        $templateData = ['passwordResetLink'  => $passwordResetLink];

        $mailService->send($mailOptions, $templateData);

        $uri = $router->pathFor('frontend_login');
        Oforge()->View()->Flash()
                ->addMessage('success', I18N::translate('password_reset_mail_send', 'You will receive an email with your password change information.'));

        return $response->withRedirect($uri, 302);
    }

    /**
     * @param Request $request
     * @param Response $response
     *
     * @return Response
     * @throws ServiceNotFoundException
     * @throws ContainerException
     */
    public function resetAction(Request $request, Response $response) {
        // show the reset password form

        /** @var PasswordResetService $passwordResetService */
        /** @var Router $router */
        $passwordResetService = Oforge()->Services()->get('password.reset');
        $router               = Oforge()->Container()->get('router');
        $guid                 = $request->getParam('reset');
        $uri                  = $router->pathFor('frontend_login');

        /**
         * No guid
         */
        if (!$guid) {
            Oforge()->View()->Flash()->addMessage('warning', I18N::translate('password_reset_invalid_link', 'Invalid link.'));

            return $response->withRedirect($uri, 302);
        }

        /**
         * Reset link is not valid
         */
        if (!$passwordResetService->isResetLinkValid($guid)) {
            Oforge()->View()->Flash()->addMessage('warning', I18N::translate('password_reset_invalid_link', 'Invalid link.'));

            return $response->withRedirect($uri, 302);
        }

        Oforge()->View()->assign(['guid' => $guid]);

        return $response;
    }

    /**
     * @param Request $request
     * @param Response $response
     *
     * @return Response
     * @throws ORMException
     * @throws OptimisticLockException
     * @throws ServiceNotFoundException
     */
    public function changeAction(Request $request, Response $response) {
        /** @var SessionManagementService $sessionManagementService */ /** @var PasswordResetService $passwordResetService */ /** @var AuthService $authService */
        /** @var PasswordService $passwordService */
        $sessionManagementService = Oforge()->Services()->get('session.management');
        $passwordResetService     = Oforge()->Services()->get('password.reset');
        $authService              = Oforge()->Services()->get('auth');
        $passwordService          = Oforge()->Services()->get('password');
        $router                   = Oforge()->App()->getContainer()->get('router');
        $body                     = $request->getParsedBody();
        $guid                     = $body['guid'];
        $token                    = $body['token'];
        $password                 = $body['password_change'];
        $passwordConfirm          = $body['password_change_confirm'];
        $uri                      = $router->pathFor('frontend_login');
        $jwt                      = null;
        $user                     = null;

        /**
         * no valid form data found
         */
        if (!$guid || !$token || !$password || !$passwordConfirm) {
            Oforge()->View()->Flash()->addMessage('warning', I18N::translate('form_invalid_data', 'Invalid form data.'));

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
         * Passwords are not identical
         */
        if ($password !== $passwordConfirm) {
            Oforge()->View()->Flash()->addMessage('warning', I18N::translate('form_password_mismatch', 'Passwords do not match.'));

            return $response->withRedirect($uri, 302);
        }

        $password = $passwordService->hash($password);
        $user     = $passwordResetService->changePassword($guid, $password);

        /*
         * User not found
         */
        if (!$user) {
            Oforge()->View()->Flash()->addMessage('warning', I18N::translate('user_not_found', 'User not found.'));

            return $response->withRedirect($uri, 302);
        }

        $jwt = $authService->createJWT($user);

        /**
         * $jwt is null if the login credentials are incorrect
         */
        if (!isset($jwt)) {
            Oforge()->View()->Flash()->addMessage('warning', I18N::translate('invalid_login_credentials', 'Invalid login credentials.'));

            return $response->withRedirect($uri, 302);
        }

        $sessionManagementService->regenerateSession();
        $_SESSION['auth'] = $jwt;

        $uri = $router->pathFor('frontend_account_dashboard');
        Oforge()->View()->Flash()->addMessage('success',
            I18N::translate('password_changed_successfully', 'You have successfully changed your password. You are now logged in.'));

        return $response->withRedirect($uri, 302);
    }

}
