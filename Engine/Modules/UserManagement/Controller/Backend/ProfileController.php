<?php
/**
 * Created by PhpStorm.
 * User: Alexander Wegner
 * Date: 19.12.2018
 * Time: 12:51
 */

namespace Oforge\Engine\Modules\UserManagement\Controller\Backend;

use Exception;
use Oforge\Engine\Modules\AdminBackend\Core\Abstracts\SecureBackendController;
use Oforge\Engine\Modules\AdminBackend\Core\Services\DashboardWidgetsService;
use Oforge\Engine\Modules\Auth\Models\User\BackendUser;
use Oforge\Engine\Modules\Auth\Services\AuthService;
use Oforge\Engine\Modules\Core\Annotation\Endpoint\EndpointAction;
use Oforge\Engine\Modules\Core\Annotation\Endpoint\EndpointClass;
use Oforge\Engine\Modules\Core\Exceptions\NotFoundException;
use Oforge\Engine\Modules\Core\Exceptions\ServiceNotFoundException;
use Oforge\Engine\Modules\Core\Helper\RouteHelper;
use Oforge\Engine\Modules\Core\Services\Session\SessionManagementService;
use Oforge\Engine\Modules\I18n\Helper\I18N;
use Oforge\Engine\Modules\UserManagement\Services\BackendUsersCrudService;
use Slim\Http\Request;
use Slim\Http\Response;

/**
 * Class ProfileController
 *
 * @package Oforge\Engine\Modules\UserManagement\Controller\Backend
 * @EndpointClass(path="/backend/profile", name="backend_profile", assetScope="Backend")
 */
class ProfileController extends SecureBackendController {

    public function initPermissions() {
        $this->ensurePermissions([
            'indexAction',
            'loginDataAction',
            'detailsAction',
            'dashboardWidgetsAction',
        ], BackendUser::ROLE_MODERATOR);
    }

    /**
     * @param Request $request
     * @param Response $response
     * @EndpointAction()
     */
    public function indexAction(Request $request, Response $response) {
    }

    /**
     * Update one's own user profile.
     * If the password hasn't been changed (post value is empty) the password part gets removed, so it won't be updated.
     * The new user data has to be updated also in the session. A user cannot change his/her own role
     *
     * @param Request $request
     * @param Response $response
     *
     * @return Response
     * @throws NotFoundException
     * @throws ServiceNotFoundException
     * @EndpointAction()
     */
    public function updateAction(Request $request, Response $response) {
        if ($request->isPost()) {
        }

        return RouteHelper::redirect($response, 'backend_profile');
    }

    /**
     * @param Request $request
     * @param Response $response
     *
     * @return Response
     * @EndpointAction(path="/login", name="login")
     */
    public function loginDataAction(Request $request, Response $response) {
        $postData = $request->getParsedBody();
        if ($request->isPost() && isset($postData['data'])) {
            try {
                $user = $postData['data'];
                /** @var BackendUsersCrudService $backendUserService */
                $backendUserService = Oforge()->Services()->get('backend.users.crud');
                if (isset($user['password']) && $user['password'] === '') {
                    unset($user['password']);
                }
                /** @var AuthService $authService */
                $authService  = Oforge()->Services()->get('auth');
                $oldUser      = $authService->decode($_SESSION['auth']);
                $user['id']   = $oldUser['id'];
                $user['type'] = $oldUser['type'];
                $user['role'] = $oldUser['role'];
                $backendUserService->update($user);
                /** @var SessionManagementService $sessionManagement */
                $sessionManagement = Oforge()->Services()->get('session.management');
                $sessionManagement->regenerateSession();
                $_SESSION['auth'] = $authService->createJWT($user);
                Oforge()->View()->Flash()->addMessage('success', I18N::translate('profile_login_data_update_success', [
                    'en' => 'Login data successfully updated',
                    'de' => 'Anmeldedaten erfolgreich aktualisiert',
                ]));
            } catch (Exception $exception) {
                Oforge()->View()->Flash()->addMessage('success', I18N::translate('profile_login_data_update_fail', [
                    'en' => 'Could not update login data',
                    'de' => 'Anmeldedaten konnten nicht aktualisiert werden',
                ]));
            }
            $response = RouteHelper::redirect($response);
        }

        return $response;
    }

    /**
     * @param Request $request
     * @param Response $response
     *
     * @return Response
     * @EndpointAction()
     */
    public function detailsAction(Request $request, Response $response) {
        $postData = $request->getParsedBody();
        if ($request->isPost()) {
            //TODO
            return RouteHelper::redirect($response);
        }
    }

    /**
     * @param Request $request
     * @param Response $response
     *
     * @return Response
     * @EndpointAction(name="dashboard_widgets")
     */
    public function dashboardWidgetsAction(Request $request, Response $response) {
        $postData = $request->getParsedBody();
        if ($request->isPost() && isset($postData['data'])) {
            $data = $postData['data'];
            try {
                /**  @var DashboardWidgetsService $dashboardWidgetsService */
                $dashboardWidgetsService = Oforge()->Services()->get('backend.dashboard.widgets');
                $dashboardWidgetsService->saveUserSettings($data);
            } catch (ServiceNotFoundException $exception) {
            }
            $response = RouteHelper::redirect($response);
        }

        return $response;
    }

}
