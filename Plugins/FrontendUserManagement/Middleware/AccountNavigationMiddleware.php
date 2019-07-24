<?php
namespace FrontendUserManagement\Middleware;

use FrontendUserManagement\Services\AccountNavigationService;
use Oforge\Engine\Modules\Core\Services\ConfigService;
use Oforge\Engine\Modules\I18n\Helper\I18N;
use Oforge\Engine\Modules\I18n\Services\InternationalizationService;
use Oforge\Engine\Modules\I18n\Services\LanguageService;
use Slim\Http\Request;
use Slim\Http\Response;

class AccountNavigationMiddleware {
    public function append(Request $request, Response $response) {
        if (isset($_SESSION['user_logged_in']) && $_SESSION['user_logged_in'] === true) {

            /** @var AccountNavigationService $accountNavigationService */
            $accountNavigationService = Oforge()->Services()->get('frontend.user.management.account.navigation');
            $sidebarNavigation = $accountNavigationService->get('sidebar');

            /** @var ConfigService $configService */
            $configService = Oforge()->Services()->get('config');
            $projectName = $configService->get('system_project_name');

            $title = I18N::translate('your_account', 'Your Account') . I18N::translate('title_separator', ['en' => ' | ', 'de' => ' | ']) . $projectName;

            Oforge()->View()->assign(['sidebar_navigation' => $sidebarNavigation, 'meta' => ['title' =>  $title ]]);
        }
    }
}
