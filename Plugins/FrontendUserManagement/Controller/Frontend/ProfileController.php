<?php

namespace FrontendUserManagement\Controller\Frontend;

use FrontendUserManagement\Abstracts\SecureFrontendController;
use FrontendUserManagement\Models\User;
use FrontendUserManagement\Services\ProfileNavigationService;
use Slim\Http\Request;
use Slim\Http\Response;

class ProfileController extends SecureFrontendController {
    public function indexAction(Request $request, Response $response) {

        /** @var ProfileNavigationService $profileNavigationService */
        $profileNavigationService = Oforge()->Services()->get('frontend.user.management.profile.navigation');
        $sidebarNavigation = $profileNavigationService->get('sidebar');

        Oforge()->View()->assign(['sidebar_navigation' => $sidebarNavigation, 'content' => $sidebarNavigation]);
    }
    public function editAction(Request $request, Response $response) {
    }
    public function deleteAction(Request $request, Response $response) {
    }
    
    /**
     * @throws \Oforge\Engine\Modules\Core\Exceptions\ServiceNotFoundException
     */
    public function initPermissions() {
        $this->ensurePermissions("indexAction", User::class);
        $this->ensurePermissions("editAction", User::class);
        $this->ensurePermissions("deleteAction", User::class);
    }
}
