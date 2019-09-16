<?php

namespace FrontendUserManagement\Controller\Frontend;

use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use FrontendUserManagement\Abstracts\SecureFrontendController;
use FrontendUserManagement\Models\User;
use FrontendUserManagement\Services\AccountNavigationService;
use FrontendUserManagement\Services\PasswordResetService;
use Insertion\Services\InsertionProfileProgressService;
use Oforge\Engine\Modules\Auth\Services\AuthService;
use Oforge\Engine\Modules\Auth\Services\PasswordService;
use Oforge\Engine\Modules\Core\Annotation\Endpoint\EndpointAction;
use Oforge\Engine\Modules\Core\Annotation\Endpoint\EndpointClass;
use Oforge\Engine\Modules\Core\Exceptions\ServiceNotFoundException;
use Oforge\Engine\Modules\Core\Services\Session\SessionManagementService;
use Oforge\Engine\Modules\I18n\Helper\I18N;
use Slim\Http\Request;
use Slim\Http\Response;
use Slim\Router;

/**
 * Class AccountController
 *
 * @package FrontendUserManagement\Controller\Frontend
 * @EndpointClass(path="/account", name="frontend_account", assetScope="Frontend")
 */
class AccountController extends SecureFrontendController {

    /**
     * @param Request $request
     * @param Response $response
     *
     * @return Response
     * @EndpointAction()
     */
    public function indexAction(Request $request, Response $response) {
        /** @var Router $router */
        $router = Oforge()->App()->getContainer()->get('router');
        $uri    = $router->pathFor('frontend_account_dashboard');

        if (isset($_SESSION['flashMessages'])) {
            $twigFlash = Oforge()->View()->Flash();
            // Oforge()->View()->addFlashMessage($_SESSION['flashMessage']['type'], $_SESSION['flashMessage']['message']);
            foreach ($_SESSION['flashMessages'] as $index => $entry) {
                $twigFlash->addMessageArray($entry);
            }
        }

        return $response->withRedirect($uri);
    }

    /**
     * @param Request $request
     * @param Response $response
     *
     * @throws ORMException
     * @throws ServiceNotFoundException
     * @EndpointAction()
     */
    public function dashboardAction(Request $request, Response $response) {
        /** @var AccountNavigationService $accountNavigationService */
        $accountNavigationService = Oforge()->Services()->get('frontend.user.management.account.navigation');
        $backendNavigationService = $accountNavigationService->get('sidebar');

        Oforge()->View()->assign(['content' => $backendNavigationService]);

        /**
         *  Check Insertion Profile Progress
         */
        $keys = [
            'background',
            'description',
            'imprintName',
            'imprintZipCity',
            'imprintEmail',
        ];

        $user = Oforge()->View()->get('current_user');

        /** @var InsertionProfileProgressService $profileProgressService */
        $profileProgressService = Oforge()->Services()->get('insertion.profile.progress');
        $progress = $profileProgressService->calculateProgress($user['id'], $keys);

        if($progress < 100) {

            $message = I18N::translate('insertion_profile_progress', [
                'en' => 'Status of your Insertion:',
                'de' => 'Status deines Profils:',
            ]);

            $flashMessage = ['dismissible' => true, 'type' =>'info', 'message' => $message . ' ' .  $progress . '%'];
            Oforge()->View()->assign(['flashMessages' => array($flashMessage)]);
        }

        if(Oforge()->View()->Flash()->hasData('new_registration')) {
            $newRegistration = Oforge()->View()->Flash()->getData('new_registration');
            Oforge()->View()->assign($newRegistration);
            Oforge()->View()->Flash()->clearData('new_registration');
        }

    }

    /**
     * @param Request $request
     * @param Response $response
     * @EndpointAction()
     */
    public function editAction(Request $request, Response $response) {
        //
    }

    /**
     * @param Request $request
     * @param Response $response
     *
     * @return Response
     * @throws ORMException
     * @throws OptimisticLockException
     * @throws ServiceNotFoundException
     * @EndpointAction()
     */
    public function edit_processAction(Request $request, Response $response) {
        /**
         * @var SessionManagementService $sessionManagementService
         * @var PasswordResetService $passwordResetService
         * @var AuthService $authService
         * @var PasswordService $passwordService
         */
        $sessionManagementService = Oforge()->Services()->get('session.management');
        $passwordResetService     = Oforge()->Services()->get('password.reset');
        $authService              = Oforge()->Services()->get('auth');
        $passwordService          = Oforge()->Services()->get('password');
        $router                   = Oforge()->App()->getContainer()->get('router');
        $body                     = $request->getParsedBody();
        $token                    = $body['token'];
        $password                 = $body['change_password_current_password'];
        $newPassword              = $body['change_password_new_password'];
        $passwordConfirm          = $body['change_password_new_password_confirm'];
        $uri                      = $router->pathFor('frontend_account_dashboard');
        $user                     = Oforge()->View()->get('current_user');
        $jwt                      = null;

        /**
         * no valid form data found
         */
        if (!$token || !$password || !$newPassword || !$passwordConfirm) {
            Oforge()->View()->Flash()->addMessage('warning', I18N::translate('form_invalid_data', 'Invalid form data.'));

            return $response->withRedirect($uri, 302);
        }

        /**
         * invalid token was sent
         */
        if (!hash_equals($_SESSION['token'], $body['token'])) {
            Oforge()->View()->Flash()->addMessage('warning', I18N::translate('form_invalid_token', 'The data has been sent from an invalid form.'));
            Oforge()->Logger()->get()->addWarning('Someone tried to change the password without a valid form csrf token! Redirecting back to login.');

            return $response->withRedirect($uri, 302);
        }

        if (!$_SESSION['auth']) {
            Oforge()->View()->Flash()->addMessage('warning', I18N::translate('missing_session_auth', 'The data has been sent from an invalid form.'));
            Oforge()->Logger()->get()->addWarning('Someone tried to change the password without a valid form csrf token! Redirecting back to login.');

            return $response->withRedirect($uri, 302);
        }

        if (!isset($user['guid'])) {
            Oforge()->View()->Flash()->addMessage('warning', I18N::translate('missing_user_guid', 'No guid.'));
            Oforge()->Logger()->get()->addWarning('Someone tried to change the password without a guid. Redirecting back to login.');

            return $response->withRedirect($uri, 302);
        }

        $guid = $user['guid'];

        $entityManager = Oforge()->DB()->getForgeEntityManager();
        $repository    = $entityManager->getRepository(User::class);
        $dbPassword    = $repository->find($user['id'])->getPassword();

        if (!$passwordService->validate($password, $dbPassword)) {
            Oforge()->View()->Flash()->addMessage('warning', I18N::translate('form_current_password_validation_failed', 'Current password is wrong.'));
            Oforge()->Logger()->get()->addWarning('Someone typed in the wrong current password while changing the password! Redirecting back to login.');

            return $response->withRedirect($uri, 302);
        }

        /**
         * Passwords are not identical
         */
        if ($newPassword !== $passwordConfirm) {
            Oforge()->View()->Flash()->addMessage('warning', I18N::translate('form_password_mismatch', 'Passwords do not match.'));

            return $response->withRedirect($uri, 302);
        }

        $newPassword = $passwordService->hash($newPassword);
        $user        = $passwordResetService->changePassword($guid, $newPassword);

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

        Oforge()->View()->Flash()->addMessage('success', I18N::translate('password_updated_successfully', 'You have successfully changed your password.'));
        $uri = $router->pathFor('frontend_account_edit');

        return $response->withRedirect($uri, 302);
    }

    /**
     * @param Request $request
     * @param Response $response
     * @EndpointAction()
     */
    public function deleteAction(Request $request, Response $response) {
        //
    }

    /**
     * @param Request $request
     * @param Response $response
     * @EndpointAction()
     */
    public function delete_processAction(Request $request, Response $response) {
        //
    }

    /**
     * @throws ServiceNotFoundException
     */
    public function initPermissions() {
        $this->ensurePermissions([
            'indexAction',
            'dashboardAction',
            'editAction',
            'edit_processAction',
            'deleteAction',
            'delete_processAction',
        ]);
    }

}
