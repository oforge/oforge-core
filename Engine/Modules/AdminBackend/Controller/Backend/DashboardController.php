<?php
namespace Oforge\Engine\Modules\AdminBackend\Controller\Backend;

use Oforge\Engine\Modules\AdminBackend\Abstracts\SecureBackendController;
use Oforge\Engine\Modules\AdminBackend\Models\BackendNavigation;
use Oforge\Engine\Modules\AdminBackend\Services\BackendNavigationService;
use Oforge\Engine\Modules\Auth\Models\User\BackendUser;
use Oforge\Engine\Modules\Auth\Services\AuthService;
use Slim\Http\Request;
use Slim\Http\Response;

class DashboardController extends SecureBackendController {
    
    /**
     * @param Request $request
     * @param Response $response
     *
     * @throws \Oforge\Engine\Modules\Core\Exceptions\ServiceNotFoundException
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
     * @throws \Oforge\Engine\Modules\Core\Exceptions\ServiceNotFoundException
     */
    public function buildAction(Request $request, Response $response) {
        Oforge()->Services()->get("assets.template")->build(Oforge()->View()->get("meta")["asset_scope"]);
    }

    public function fontAwesomeAction(Request $request, Response $response) {
    }

    public function ioniconsAction(Request $request, Response $response) {
    }

    public function helpAction(Request $request, Response $response) {
    }
    
    /**
     * @param Request $request
     * @param Response $response
     *
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     * @throws \Oforge\Engine\Modules\Core\Exceptions\ConfigElementAlreadyExists
     * @throws \Oforge\Engine\Modules\Core\Exceptions\ConfigOptionKeyNotExists
     * @throws \Oforge\Engine\Modules\Core\Exceptions\ParentNotFoundException
     * @throws \Oforge\Engine\Modules\Core\Exceptions\ServiceNotFoundException
     */
    public function testAction(Request $request, Response $response) {
        /**
         * @var $sidebarNavigation BackendNavigationService
         */
        $sidebarNavigation = Oforge()->Services()->get("backend.navigation");


        $sidebarNavigation->put([
            "name" => "admin",
            "order" => 99
        ]);


        $sidebarNavigation->put([
            "name" => "help",
            "order" => 99,
            "parent" => "admin",
            "icon" => "ion-help"
        ]);

        $sidebarNavigation->put([
            "name" => "ionicons",
            "order" => 2,
            "parent" => "help",
            "icon" => "ion-nuclear",
            "path" => "backend_dashboard_ionicons"
        ]);

        $sidebarNavigation->put([
            "name" => "fontAwesome",
            "order" => 1,
            "parent" => "help",
            "icon" => "fa-fort-awesome",
            "path" => "backend_dashboard_fontAwesome"
        ]);

    }


    public function initPermissions() {
        $this->ensurePermissions("indexAction", BackendUser::class, BackendUser::ROLE_MODERATOR);
//        $this->ensurePermissions("indexAction", BackendUser::class, BackendUser::ROLE_MODERATOR);
    }
}
