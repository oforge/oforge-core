<?php

namespace FrontendUserManagement\Controller\Frontend;

use FrontendUserManagement\Abstracts\SecureFrontendController;
use FrontendUserManagement\Models\User;
use FrontendUserManagement\Services\AccountNavigationService;
use Slim\Http\Request;
use Slim\Http\Response;
use Slim\Router;

class AccountController extends SecureFrontendController {
    public function indexAction(Request $request, Response $response) {

        /** @var Router $router */
        $router = Oforge()->App()->getContainer()->get('router');
        $uri = $router->pathFor('frontend_account_dashboard');

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
    public function edit_processAction(Request $request, Response $response) {

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
