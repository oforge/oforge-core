<?php

namespace FrontendUserManagement;

use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use FrontendUserManagement\Middleware\AccountNavigationMiddleware;
use FrontendUserManagement\Middleware\FrontendSecureMiddleware;
use FrontendUserManagement\Middleware\FrontendUserStateMiddleware;
use FrontendUserManagement\Models\AccountNavigation;
use FrontendUserManagement\Models\User;
use FrontendUserManagement\Models\UserAddress;
use FrontendUserManagement\Models\UserDetail;
use FrontendUserManagement\Services\AccountNavigationService;
use FrontendUserManagement\Widgets\DashboardWidgetHandler;
use Oforge\Engine\Modules\AdminBackend\Core\Services\DashboardWidgetsService;
use Oforge\Engine\Modules\Core\Abstracts\AbstractBootstrap;
use Oforge\Engine\Modules\Core\Exceptions\ConfigElementAlreadyExistException;
use Oforge\Engine\Modules\Core\Exceptions\ConfigOptionKeyNotExistException;
use Oforge\Engine\Modules\Core\Exceptions\ParentNotFoundException;
use Oforge\Engine\Modules\Core\Exceptions\ServiceNotFoundException;

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
        ];

        $this->middlewares = [
            'frontend'         => [
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
        ];

        $this->services = [
            'frontend.user.management.password.reset'     => Services\PasswordResetService::class,
            'frontend.user.management.login'              => Services\FrontendUserLoginService::class,
            'frontend.user.management.registration'       => Services\RegistrationService::class,
            'frontend.user.management.account.navigation' => Services\AccountNavigationService::class,
            'frontend.user.management.user'               => Services\UserService::class,
            'frontend.user.management.user.details'       => Services\UserDetailsService::class,
            'frontend.user.management.user.address'       => Services\UserAddressService::class,
            "frontend.user"                               => Services\FrontendUserService::class,
            'password.reset'                              => Services\PasswordResetService::class,
        ];
    }

    /**
     * @throws ORMException
     * @throws OptimisticLockException
     * @throws ConfigElementAlreadyExistException
     * @throws ConfigOptionKeyNotExistException
     * @throws ParentNotFoundException
     * @throws ServiceNotFoundException
     */
    public function activate() {
        /** @var AccountNavigationService $accountNavigationService */
        $accountNavigationService = Oforge()->Services()->get('frontend.user.management.account.navigation');

        $accountNavigationService->put([
            'name'     => 'frontend_account_details',
            'order'    => 1,
            'icon'     => 'contact',
            'path'     => 'frontend_account_details',
            'position' => 'sidebar',
        ]);
        $accountNavigationService->put([
            'name'     => 'frontend_account_edit',
            'order'    => 1,
            'icon'     => 'profile',
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

        /** @var DashboardWidgetsService $dashboardWidgetsService */
        $dashboardWidgetsService = Oforge()->Services()->get('backend.dashboard.widgets');
        $dashboardWidgetsService->register([
            'position'     => 'top',
            'action'       => DashboardWidgetHandler::class,
            'title'        => 'frontend_users_title',
            'name'         => 'frontend_users',
            'cssClass'     => 'bg-yellow',
            'templateName' => 'FrontendUsers',
        ]);
    }

    /**
     * @throws ServiceNotFoundException
     */
    public function deactivate() {
        /** @var DashboardWidgetsService $dashboardWidgetsService */
        $dashboardWidgetsService = Oforge()->Services()->get('backend.dashboard.widgets');
        $dashboardWidgetsService->unregister("frontend_users");
    }

}
