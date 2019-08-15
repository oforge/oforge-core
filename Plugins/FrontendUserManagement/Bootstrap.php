<?php

namespace FrontendUserManagement;

use FrontendUserManagement\Middleware\AccountNavigationMiddleware;
use FrontendUserManagement\Middleware\FrontendSecureMiddleware;
use FrontendUserManagement\Middleware\FrontendUserStateMiddleware;
use FrontendUserManagement\Models\AccountNavigation;
use FrontendUserManagement\Models\NickNameValue;
use FrontendUserManagement\Models\User;
use FrontendUserManagement\Models\UserAddress;
use FrontendUserManagement\Models\UserDetail;
use FrontendUserManagement\Services\AccountNavigationService;
use FrontendUserManagement\Widgets\DashboardWidgetHandler;
use Oforge\Engine\Modules\AdminBackend\Core\Enums\DashboardWidgetPosition;
use Oforge\Engine\Modules\AdminBackend\Core\Services\BackendNavigationService;
use Oforge\Engine\Modules\AdminBackend\Core\Services\DashboardWidgetsService;
use Oforge\Engine\Modules\Core\Abstracts\AbstractBootstrap;

/**
 * Class Bootstrap
 *
 * @package FrontendUserManagement
 */
class Bootstrap extends AbstractBootstrap {

    public function __construct() {
        $this->endpoints = [
            Controller\Frontend\LoginController::class,
            Controller\Frontend\LoginRegistrationController::class,
            Controller\Frontend\LogoutController::class,
            Controller\Frontend\RegistrationController::class,
            Controller\Frontend\ForgotPasswordController::class,
            Controller\Frontend\AccountController::class,
            Controller\Frontend\UserDetailsController::class,
            Controller\Backend\BackendFrontendUserManagementController::class,
            Controller\Backend\BackendNickNameGeneratorController::class,
        ];

        $this->middlewares = [
            '*'                => [
                'class'    => FrontendUserStateMiddleware::class,
                'position' => 1,
            ],
            'frontend_account' => [
                ['class' => FrontendSecureMiddleware::class, 'position' => 1],
                ['class' => AccountNavigationMiddleware::class, 'position' => 1],
            ],
        ];

        $this->models = [
            AccountNavigation::class,
            User::class,
            UserDetail::class,
            UserAddress::class,
            NickNameValue::class,
        ];

        $this->services = [
            'frontend.user.management.password.reset'     => Services\PasswordResetService::class,
            'frontend.user.management.login'              => Services\FrontendUserLoginService::class,
            'frontend.user.management.registration'       => Services\RegistrationService::class,
            'frontend.user.management.account.navigation' => Services\AccountNavigationService::class,
            'frontend.user.management.user'               => Services\UserService::class,
            'frontend.user.management.user.details'       => Services\UserDetailsService::class,
            'frontend.user.management.user.address'       => Services\UserAddressService::class,
            'frontend.user'                               => Services\FrontendUserService::class,
            'password.reset'                              => Services\PasswordResetService::class,
        ];
    }

    /** @inheritDoc */
    public function install() {
        /** @var DashboardWidgetsService $dashboardWidgetsService */
        $dashboardWidgetsService = Oforge()->Services()->get('backend.dashboard.widgets');
        $dashboardWidgetsService->install([
            'name'     => 'plugin_frontend_user_registrations',
            'template' => 'FrontendUsersRegistrations',
            'handler'  => DashboardWidgetHandler::class,
            'label'    => [
                'en' => 'User registrations',
                'de' => 'Nutzerregistrierungen',
            ],
            'position' => DashboardWidgetPosition::TOP,
            'cssClass' => 'box-yellow',
        ]);
    }

    /** @inheritDoc */
    public function uninstall() {
        /** @var DashboardWidgetsService $dashboardWidgetsService */
        $dashboardWidgetsService = Oforge()->Services()->get('backend.dashboard.widgets');
        $dashboardWidgetsService->uninstall('plugin_frontend_user_registrations');
    }

    /** @inheritDoc */
    public function activate() {
        /** @var AccountNavigationService $accountNavigationService */
        $accountNavigationService = Oforge()->Services()->get('frontend.user.management.account.navigation');

        $accountNavigationService->put([
            'name'     => 'frontend_account_dashboard',
            'order'    => 1,
            'path'     => 'frontend_account_dashboard',
            'position' => 'sidebar',
        ]);

        $accountNavigationService->put([
            'name'     => 'frontend_account_details',
            'order'    => 1,
            'icon'     => 'contact',
            'path'     => 'frontend_account_details',
            'position' => 'sidebar',
        ]);
        $accountNavigationService->put([
            'name'     => 'frontend_account_change_password',
            'order'    => 999,
            'icon'     => 'key',
            'path'     => 'frontend_account_edit',
            'position' => 'sidebar',
        ]);
        $accountNavigationService->put([
            'name'     => 'frontend_logout',
            'order'    => 1000,
            'icon'     => 'exit',
            'path'     => 'frontend_logout',
            'position' => 'sidebar',
        ]);

        /** @var BackendNavigationService $backendNavigationService */
        $backendNavigationService = Oforge()->Services()->get('backend.navigation');
        $backendNavigationService->add(BackendNavigationService::CONFIG_CONTENT);
        $backendNavigationService->add([
            'name'     => 'backend_frontend_user_management',
            'order'    => 4,
            'parent'   => BackendNavigationService::KEY_CONTENT,
            'icon'     => 'fa fa-user',
            'position' => 'sidebar',
        ]);
        $backendNavigationService->add([
            'name'     => 'backend_frontend_user_management_list',
            'order'    => 1,
            'icon'     => 'fa fa-list',
            'path'     => 'backend_frontend_user_management',
            'parent'   => 'backend_frontend_user_management',
            'position' => 'sidebar',
        ]);
        $backendNavigationService->add([
            'name'     => 'backend_frontend_user_management_nickname_generator',
            'order'    => 2,
            'icon'     => 'fa fa-brain',
            'path'     => 'backend_frontend_user_management_nickname_generator',
            'parent'   => 'backend_frontend_user_management',
            'position' => 'sidebar',
        ]);
        /** @var DashboardWidgetsService $dashboardWidgetsService */
        $dashboardWidgetsService = Oforge()->Services()->get('backend.dashboard.widgets');
        $dashboardWidgetsService->activate('plugin_frontend_user_registrations');
    }

    /** @inheritDoc */
    public function deactivate() {
        /** @var DashboardWidgetsService $dashboardWidgetsService */
        $dashboardWidgetsService = Oforge()->Services()->get('backend.dashboard.widgets');
        $dashboardWidgetsService->deactivate("plugin_frontend_user_registrations");
    }

}
