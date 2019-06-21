<?php

namespace Oforge\Engine\Modules\AdminBackend\Core\Controller\Backend;

use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use Oforge\Engine\Modules\AdminBackend\Core\Abstracts\SecureBackendController;
use Oforge\Engine\Modules\AdminBackend\Core\Services\BackendNavigationService;
use Oforge\Engine\Modules\AdminBackend\Core\Services\DashboardWidgetsService;
use Oforge\Engine\Modules\Auth\Models\User\BackendUser;
use Oforge\Engine\Modules\Auth\Services\AuthService;
use Oforge\Engine\Modules\Core\Annotation\Endpoint\EndpointAction;
use Oforge\Engine\Modules\Core\Annotation\Endpoint\EndpointClass;
use Oforge\Engine\Modules\Core\Exceptions\ConfigElementAlreadyExistException;
use Oforge\Engine\Modules\Core\Exceptions\ConfigOptionKeyNotExistException;
use Oforge\Engine\Modules\Core\Exceptions\ParentNotFoundException;
use Oforge\Engine\Modules\Core\Exceptions\ServiceNotFoundException;
use Slim\Http\Request;
use Slim\Http\Response;

/**
 * Class DashboardController
 *
 * @package Oforge\Engine\Modules\AdminBackend\Core\Controller\Backend
 * @EndpointClass(path="/backend/dashboard", name="backend_dashboard", assetScope="Backend")
 */
class DashboardController extends SecureBackendController {

    /**
     * @param Request $request
     * @param Response $response
     *
     * @throws ServiceNotFoundException
     * @EndpointAction()
     */
    public function indexAction(Request $request, Response $response) {
        $data = [
            'page_header'             => 'Willkommen auf dem Dashboard',
            'page_header_description' => 'Hier finden Sie alle relevanten Informationen übersichtlich dargestellt.',
        ];
        /**
         * @var $authService AuthService
         */
        $authService  = Oforge()->Services()->get('auth');
        $user         = $authService->decode($_SESSION['auth']);
        $data['user'] = $user;

        Oforge()->View()->assign($data);
    }

    /**
     * @param Request $request
     * @param Response $response
     *
     * @throws ServiceNotFoundException
     * @EndpointAction()
     */
    public function buildAction(Request $request, Response $response) {
        Oforge()->Services()->get('assets.template')->build("", Oforge()->View()->get('meta')['route']['assetScope']);
    }

    /**
     * @param Request $request
     * @param Response $response
     * @EndpointAction()
     */
    public function widgetsAction(Request $request, Response $response) {
        if ($_POST && isset($_POST["data"])) {
            $data = json_decode($_POST["data"], true);

            $auth = null;
            if (isset($_SESSION['auth'])) {
                $auth = $_SESSION['auth'];
            }

            /** @var AuthService $authService */
            $authService = Oforge()->Services()->get('auth');
            $user        = $authService->decode($auth);

            if ($user != null) {
                /** @var AuthService $authService */
                $authService = Oforge()->Services()->get('auth');
                $user        = $authService->decode($auth);

                /**
                 * @var DashboardWidgetsService $dashboardWidgetsService
                 */
                $dashboardWidgetsService = Oforge()->Services()->get('backend.dashboard.widgets');
                $dashboardWidgetsService->updateUserWidgets($user["id"], $data);
            }

            Oforge()->View()->assign(["json" => $data]);
        }

    }

    /**
     * @param Request $request
     * @param Response $response
     * @EndpointAction()
     */
    public function fontAwesomeAction(Request $request, Response $response) {
    }

    /**
     * @param Request $request
     * @param Response $response
     * @EndpointAction()
     */
    public function ioniconsAction(Request $request, Response $response) {
    }

    /**
     * @param Request $request
     * @param Response $response
     * @EndpointAction()
     */
    public function helpAction(Request $request, Response $response) {
    }

    /**
     * @param Request $request
     * @param Response $response
     *
     * @throws ORMException
     * @throws OptimisticLockException
     * @throws ConfigElementAlreadyExistException
     * @throws ConfigOptionKeyNotExistException
     * @throws ServiceNotFoundException
     * @throws ParentNotFoundException
     * @EndpointAction()
     */
    public function testAction(Request $request, Response $response) {
        /** @var BackendNavigationService $sidebarNavigation */
        $sidebarNavigation = Oforge()->Services()->get('backend.navigation');

        $sidebarNavigation->put([
            'name'     => 'admin',
            'order'    => 99,
            'position' => 'sidebar',
        ]);
        $sidebarNavigation->put([
            'name'     => 'help',
            'order'    => 99,
            'parent'   => 'admin',
            'icon'     => 'ion-help',
            'position' => 'sidebar',
        ]);
        $sidebarNavigation->put([
            'name'     => 'ionicons',
            'order'    => 2,
            'parent'   => 'help',
            'icon'     => 'ion-nuclear',
            'path'     => 'backend_dashboard_ionicons',
            'position' => 'sidebar',
        ]);
        $sidebarNavigation->put([
            'name'     => 'fontAwesome',
            'order'    => 1,
            'parent'   => 'help',
            'icon'     => 'fa-fort-awesome',
            'path'     => 'backend_dashboard_fontAwesome',
            'position' => 'sidebar',
        ]);
    }

    /**
     * @throws ServiceNotFoundException
     */
    public function initPermissions() {
        $this->ensurePermissions('indexAction', BackendUser::class, BackendUser::ROLE_MODERATOR);
        $this->ensurePermissions('buildAction', BackendUser::class, BackendUser::ROLE_ADMINISTRATOR);
        $this->ensurePermissions('widgetsAction', BackendUser::class, BackendUser::ROLE_MODERATOR);
        $this->ensurePermissions('testAction', BackendUser::class, BackendUser::ROLE_ADMINISTRATOR);
    }

}
