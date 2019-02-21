<?php
/**
 * Created by PhpStorm.
 * User: Alexander Wegner
 * Date: 11.02.2019
 * Time: 10:05
 */

namespace FrontendUserManagement\Controller\Frontend;

use FrontendUserManagement\Services\PasswordResetService;
use Oforge\Engine\Modules\Auth\Services\AuthService;
use Oforge\Engine\Modules\Auth\Services\PasswordService;
use Oforge\Engine\Modules\Core\Abstracts\AbstractController;
use Oforge\Engine\Modules\Session\Services\SessionManagementService;
use Slim\Http\Request;
use Slim\Http\Response;
use Slim\Router;

class ForgotPasswordController extends AbstractController {
    public function indexAction(Request $request, Response $response) {
        // show the email form for requesting a reset link
    }

    /**
     * @param Request $request
     * @param Response $response
     *
     * @return Response
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     * @throws \Interop\Container\Exception\ContainerException
     * @throws \Oforge\Engine\Modules\Core\Exceptions\ServiceNotFoundException
     */
    public function processAction(Request $request, Response $response) {
        /** @var PasswordResetService $passwordResetService */
        /** @var Router $router */
        $passwordResetService   = Oforge()->Services()->get('password.reset');
        $router                 = Oforge()->Container()->get('router');
        $body                   = $request->getParsedBody();
        $email                  = $body['forgot-password__email'];
        $token                  = $body['token'];
        $uri                    = $router->pathFor('frontend_forgot_password');

        /**
         * no token was sent
         */
        if (!isset($body['token']) || empty($body['token'])) {
            Oforge()->Logger()->get()->addWarning('Someone tried to do a backend login with a form without csrf token! Redirecting to backend login.');

            return $response->withRedirect($uri, 302);
        }

        /**
         * invalid token was sent
         */
        if (!hash_equals($_SESSION['token'], $body['token'])) {
            Oforge()->Logger()->get()->addWarning('Someone tried a backend login without a valid form csrf token! Redirecting back to login.');
            return $response->withRedirect($uri, 302);
        }

        /**
         * no email body was sent
         */
        if (!$email) {
            return $response->withRedirect($uri, 302);
        }

        /**
         * Email not found
         */
        if (!$passwordResetService->emailExists($email)) {
            return $response->withRedirect($uri, 302);
        }

        $passwordResetLink = $passwordResetService->createPasswordResetLink($email);

        $mailService = Oforge()->Services()->get('mail');

        // TODO: add email snippets
        $mailOptions = [
            'to' => [$email => $email],
            'subject' => 'Oforge | Your password reset!',
            'body' => 'You want to reset your password. Your password reset link is: '.$passwordResetLink
        ];

        $mailService->send($mailOptions);

        $uri = $router->pathFor('frontend_login');

        return $response->withRedirect($uri, 302);
    }

    /**
     * @param Request $request
     * @param Response $response
     *
     * @return Response
     * @throws \Oforge\Engine\Modules\Core\Exceptions\ServiceNotFoundException
     * @throws \Interop\Container\Exception\ContainerException
     */
    public function resetAction(Request $request, Response $response) {
        // show the reset password form
        /** @var PasswordResetService $passwordResetService */
        /** @var Router $router */
        $passwordResetService   = Oforge()->Services()->get('password.reset');
        $router                 = Oforge()->Container()->get('router');
        $guid                   = $request->getParam('reset');
        $uri                    = $router->pathFor('frontend_login');

        /**
         * No guid
         */
        if (!$guid) {
            return $response->withRedirect($uri, 302);
        }

        /**
         * Reset link is not valid
         */
        if (!$passwordResetService->isResetLinkValid($guid)) {
            return $response->withRedirect($uri, 302);
        }

        Oforge()->View()->assign(['guid' => $guid]);

        return $response;
    }

    /**
     * Change the password
     *
     * @param Request $request
     * @param Response $response
     *
     * @return Response
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     * @throws \Oforge\Engine\Modules\Core\Exceptions\ServiceNotFoundException
     */
    public function changeAction(Request $request, Response $response) {
        /** @var SessionManagementService $sessionManagementService */
        /** @var PasswordResetService $passwordResetService */
        /** @var AuthService $authService */
        /** @var PasswordService $passwordService */
        $sessionManagementService   = Oforge()->Services()->get('session.management');
        $passwordResetService       = Oforge()->Services()->get('password.reset');
        $authService                = Oforge()->Services()->get('auth');
        $passwordService            = Oforge()->Services()->get('password');
        $router                     = Oforge()->App()->getContainer()->get('router');
        $body                       = $request->getParsedBody();
        $guid                       = $body['guid'];
        $token                      = $body['token'];
        $password                   = $body['password_change'];
        $passwordConfirm            = $body['password_change_confirm'];
        $uri                        = $router->pathFor('frontend_login');
        $jwt                        = null;
        $user                       = null;

        /**
         * no valid form data found
         */
        if (!$guid||!$token||!$password||!$passwordConfirm) {
            return $response->withRedirect($uri, 302);
        }

        /**
         * invalid token was sent
         */
        if (!hash_equals($_SESSION['token'], $body['token'])) {
            Oforge()->Logger()->get()->addWarning('Someone tried a backend login without a valid form csrf token! Redirecting back to login.');
            return $response->withRedirect($uri, 302);
        }

        /**
         * Passwords are not identical
         */
        if ($password !== $passwordConfirm) {
            return $response->withRedirect($uri, 302);
        }

        $password = $passwordService->hash($password);
        $user = $passwordResetService->changePassword($guid, $password);

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

        $uri = $router->pathFor('frontend_profile');

        return $response->withRedirect($uri, 302);
    }
}