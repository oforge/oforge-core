<?php
namespace FrontendUserManagement\Middleware;

use FrontendUserManagement\Services\ProfileNavigationService;
use Slim\Http\Request;
use Slim\Http\Response;

class ProfileNavigationMiddleware {
    public function append(Request $request, Response $response) {
        if (isset($_SESSION['user_logged_in']) && $_SESSION['user_logged_in'] === true) {

            /** @var ProfileNavigationService $profileNavigationService */
            $profileNavigationService = Oforge()->Services()->get('frontend.user.management.profile.navigation');
            $sidebarNavigation = $profileNavigationService->get('sidebar');
            Oforge()->View()->assign(['sidebar_navigation' => $sidebarNavigation]);
        }
    }
}