<?php
namespace Oforge\Engine\Modules\AdminBackend\Core\Controller\Backend;

use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use Oforge\Engine\Modules\AdminBackend\Core\Abstracts\SecureBackendController;
use Oforge\Engine\Modules\AdminBackend\Core\Services\BackendNavigationService;
use Oforge\Engine\Modules\Auth\Models\User\BackendUser;
use Oforge\Engine\Modules\Auth\Services\AuthService;
use Oforge\Engine\Modules\Core\Exceptions\ConfigElementAlreadyExistsException;
use Oforge\Engine\Modules\Core\Exceptions\ConfigOptionKeyNotExistsException;
use Oforge\Engine\Modules\Core\Exceptions\ParentNotFoundException;
use Oforge\Engine\Modules\Core\Exceptions\ServiceNotFoundException;
use Slim\Http\Request;
use Slim\Http\Response;

class DashboardController extends SecureBackendController {
    
    /**
     * @param Request $request
     * @param Response $response
     *
     * @throws ServiceNotFoundException
     */
    public function indexAction(Request $request, Response $response) {
        $data = ['page_header' => 'Willkommen auf dem Dashboard', 'page_header_description' => "Hier finden Sie alle relevanten Informationen übersichtlich dargestellt."];
        /**
         * @var $authService AuthService
         */
        $authService = Oforge()->Services()->get("auth");
        $user = $authService->decode($_SESSION["auth"]);
        $data["user"] = $user;


        
        Oforge()->View()->assign($data);
    }
    
    /**
     * @param Request $request
     * @param Response $response
     *
     * @throws ServiceNotFoundException
     */
    public function buildAction(Request $request, Response $response) {
        Oforge()->Services()->get("assets.template")->build(Oforge()->View()->get("meta")["asset_scope"]);
    }

    /**
     * @param Request $request
     * @param Response $response
     *
     * @throws ORMException
     * @throws OptimisticLockException
     * @throws ConfigElementAlreadyExistsException
     * @throws ConfigOptionKeyNotExistsException
     * @throws ParentNotFoundException
     * @throws ServiceNotFoundException
     */
    public function testAction(Request $request, Response $response) {
        /**
         * @var $sidebarNavigation BackendNavigationService
         */
        $sidebarNavigation = Oforge()->Services()->get("backend.navigation");


        $sidebarNavigation->put([
            "name" => "admin",
            "order" => 99,
            "position" => "sidebar",
        ]);


        $sidebarNavigation->put([
            "name" => "help",
            "order" => 99,
            "parent" => "admin",
            "icon" => "ion-help",
            "position" => "sidebar",
        ]);

        $sidebarNavigation->put([
            "name" => "ionicons",
            "order" => 2,
            "parent" => "help",
            "icon" => "ion-nuclear",
            "path" => "backend_dashboard_ionicons",
            "position" => "sidebar",
        ]);

        $sidebarNavigation->put([
            "name" => "fontAwesome",
            "order" => 1,
            "parent" => "help",
            "icon" => "fa-fort-awesome",
            "path" => "backend_dashboard_fontAwesome",
            "position" => "sidebar",
        ]);

    }

    /**
     * @throws ServiceNotFoundException
     */
    public function initPermissions() {
        $this->ensurePermissions("indexAction", BackendUser::class, BackendUser::ROLE_MODERATOR);
        $this->ensurePermissions("buildAction", BackendUser::class, BackendUser::ROLE_ADMINISTRATOR);
        $this->ensurePermissions("testAction", BackendUser::class, BackendUser::ROLE_ADMINISTRATOR);
    }
}
