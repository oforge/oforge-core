<?php
namespace FrontendUserManagement\Middleware;

use FrontendUserManagement\Services\AccountNavigationService;
use Slim\Http\Request;
use Slim\Http\Response;

class AccountNavigationMiddleware {
    public function append(Request $request, Response $response) {
        if (isset($_SESSION['user_logged_in']) && $_SESSION['user_logged_in'] === true) {

            /** @var AccountNavigationService $accountNavigationService */
            $accountNavigationService = Oforge()->Services()->get('frontend.user.management.account.navigation');
            $sidebarNavigation = $accountNavigationService->get('sidebar');
            Oforge()->View()->assign(['sidebar_navigation' => $sidebarNavigation]);
        }
    }
}
