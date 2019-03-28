<?php

namespace FrontendUserManagement\Controller\Frontend;

use FrontendUserManagement\Abstracts\SecureFrontendController;
use FrontendUserManagement\Models\User;
use FrontendUserManagement\Services\AccountNavigationService;
use FrontendUserManagement\Services\PasswordResetService;
use Oforge\Engine\Modules\Auth\Services\AuthService;
use Oforge\Engine\Modules\Auth\Services\PasswordService;
use Oforge\Engine\Modules\Session\Services\SessionManagementService;
use Slim\Http\Request;
use Slim\Http\Response;
use Slim\Router;

class AccountController extends SecureFrontendController {
    public function indexAction(Request $request, Response $response) {

        /** @var Router $router */
        $router = Oforge()->App()->getContainer()->get('router');
        $uri = $router->pathFor('frontend_account_dashboard');

        if (isset($_SESSION['flashMessage'])) {
            Oforge()->View()->addFlashMessage($_SESSION['flashMessage']['type'], $_SESSION['flashMessage']['message']);
        }

        return $response->withRedirect($uri);
    }

    public function dashboardAction(Request $request, Response $response) {
        /** @var AccountNavigationService $accountNavigationService */
        $accountNavigationService = Oforge()->Services()->get('frontend.user.management.account.navigation');
        $sidebarNavigation = $accountNavigationService->get('sidebar');

        Oforge()->View()->assign(['content' => $sidebarNavigation]);
    }
    public function editAction(Request $request, Response $response) {
    }

    /**
     * @param Request $request
     * @param Response $response
     *
     * @return Response
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     * @throws \Oforge\Engine\Modules\Core\Exceptions\ServiceNotFoundException
     */
    public function edit_processAction(Request $request, Response $response) {
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
        $token                      = $body['token'];
        $password                   = $body['change_password_current_password'];
        $newPassword                = $body['change_password_new_password'];
        $passwordConfirm            = $body['change_password_new_password_confirm'];
        $uri                        = $router->pathFor('frontend_account_dashboard');
        $jwt                        = null;
        $user                       = null;

        /**
         * no valid form data found
         */
        if (!$token||!$password||!$newPassword||!$passwordConfirm) {
            Oforge()->View()->addFlashMessage('warning', 'Invalid form data.');
            return $response->withRedirect($uri, 302);
        }

        /**
         * invalid token was sent
         */
        if (!hash_equals($_SESSION['token'], $body['token'])) {
            Oforge()->View()->addFlashMessage('warning', 'The data has been sent from an invalid form.');
            Oforge()->Logger()->get()->addWarning('Someone tried to change the password without a valid form csrf token! Redirecting back to login.');
            return $response->withRedirect($uri, 302);
        }
        
        if (!$_SESSION['auth']) {
            Oforge()->View()->addFlashMessage('warning', 'No JsonWebToken.');
            Oforge()->Logger()->get()->addWarning('Someone tried to change the password without a valid form csrf token! Redirecting back to login.');
            return $response->withRedirect($uri, 302);
        }

        $jwt = $_SESSION['auth'];
        $user = $authService->decode($jwt);

        if (!isset($user['guid'])) {
            Oforge()->View()->addFlashMessage('warning', 'No guid.');
            Oforge()->Logger()->get()->addWarning('Someone tried to change the password without a guid. Redirecting back to login.');
            return $response->withRedirect($uri, 302);
        }

        $guid = $user['guid'];

        $entityManager = Oforge()->DB()->getManager();
        $repository = $entityManager->getRepository(User::class);
        $dbPassword = $repository->find($user['id'])->getPassword();
        
        if (!$passwordService->validate($password, $dbPassword)) {
            Oforge()->View()->addFlashMessage('warning', 'Current password is wrong.');
            Oforge()->Logger()->get()->addWarning('Someone typed in the wrong current password while changing the password! Redirecting back to login.');
            return $response->withRedirect($uri, 302);
        }

        /**
         * Passwords are not identical
         */
        if ($newPassword !== $passwordConfirm) {
            Oforge()->View()->addFlashMessage('warning', 'Passwords do not match.');
            return $response->withRedirect($uri, 302);
        }

        $newPassword = $passwordService->hash($newPassword);
        $user = $passwordResetService->changePassword($guid, $newPassword);

        /*
         * User not found
         */
        if (!$user) {
            Oforge()->View()->addFlashMessage('warning', 'User not found.');
            return $response->withRedirect($uri, 302);
        }

        $jwt = $authService->createJWT($user);

        /**
         * $jwt is null if the login credentials are incorrect
         */
        if (!isset($jwt)) {
            Oforge()->View()->addFlashMessage('warning', 'Invalid login credentials.');
            return $response->withRedirect($uri, 302);
        }

        $sessionManagementService->regenerateSession();
        $_SESSION['auth'] = $jwt;

        $uri = $router->pathFor('frontend_account_edit');

        Oforge()->View()->addFlashMessage('success', 'You have successfully changed your password.');

        return $response->withRedirect($uri, 302);
    }

    public function deleteAction(Request $request, Response $response) {
    }

    public function delete_processAction(Request $request, Response $response) {
    }
    
    /**
     * @throws \Oforge\Engine\Modules\Core\Exceptions\ServiceNotFoundException
     */
    public function initPermissions() {
        $this->ensurePermissions("indexAction", User::class);
        $this->ensurePermissions("dashboardAction", User::class);
        $this->ensurePermissions("editAction", User::class);
        $this->ensurePermissions("edit_processAction", User::class);
        $this->ensurePermissions("deleteAction", User::class);
        $this->ensurePermissions("delete_processAction", User::class);
    }
}
