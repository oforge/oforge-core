<?php

namespace FrontendUserManagement;

use FrontendUserManagement\Middleware\FrontendSecureMiddleware;
use FrontendUserManagement\Middleware\FrontendUserStateMiddleware;
use FrontendUserManagement\Middleware\AccountNavigationMiddleware;
use FrontendUserManagement\Models\AccountNavigation;
use FrontendUserManagement\Models\User;
use FrontendUserManagement\Models\UserAddress;
use FrontendUserManagement\Models\UserDetail;
use FrontendUserManagement\Services\AccountNavigationService;
use Oforge\Engine\Modules\Core\Abstracts\AbstractBootstrap;
use Oforge\Engine\Modules\Core\Services\MiddlewareService;

class Bootstrap extends AbstractBootstrap {
    public function __construct() {
        $this->endpoints = [
            "/login" => ["controller" => Controller\Frontend\LoginController::class, "name" => "frontend_login"],
            "/login-registration" => ["controller" => Controller\Frontend\LoginRegistrationController::class, "name" => "frontend_login_registration"],
            "/logout" => ["controller" => Controller\Frontend\LogoutController::class, "name" => "frontend_logout"],
            "/registration" => ["controller" => Controller\Frontend\RegistrationController::class, "name" => "frontend_registration"],
            "/forgot-password" => ["controller" => Controller\Frontend\ForgotPasswordController::class, "name" => "frontend_forgot_password"],
            "/account" => ["controller" => Controller\Frontend\AccountController::class, "name" => "frontend_account"],
            "/account/details" => ["controller" => Controller\Frontend\UserDetailsController::class, "name" => "frontend_account_details"],
        ];
        
        $this->middleware = [
            "frontend" => [
                "class" => FrontendUserStateMiddleware::class,
                "position" => 1,
            ],
            "frontend_account" => [
                ["class" => FrontendSecureMiddleware::class, "position" => 1],
                ["class" => AccountNavigationMiddleware::class, "position" => 1],
            ]
        ];
        
        $this->models = [
            AccountNavigation::class,
            User::class,
            UserDetail::class,
            UserAddress::class,
        ];
        
        $this->services = [
            'frontend.user.management.password.reset' => Services\PasswordResetService::class,
            'frontend.user.management.login' => Services\FrontendUserLoginService::class,
            'frontend.user.management.registration' => Services\RegistrationService::class,
            'frontend.user.management.account.navigation' => Services\AccountNavigationService::class,
            'password.reset' => Services\PasswordResetService::class
        ];
    }

    /**
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     * @throws \Oforge\Engine\Modules\Core\Exceptions\ConfigElementAlreadyExists
     * @throws \Oforge\Engine\Modules\Core\Exceptions\ConfigOptionKeyNotExists
     * @throws \Oforge\Engine\Modules\Core\Exceptions\ParentNotFoundException
     * @throws \Oforge\Engine\Modules\Core\Exceptions\ServiceNotFoundException
     */
    public function activate() {
        /** @var MiddlewareService $middlewareService */
        /** @var AccountNavigationService $accountNavigationService */
        $middlewareService = Oforge()->Services()->get('middleware');
        $middlewareService->activate('frontend');

        $accountNavigationService = Oforge()->Services()->get('frontend.user.management.account.navigation');
        $accountNavigationService->put([
            "name" => "frontend_logout",
            "order" => 1000,
            "icon" => "profil",
            "path" => "frontend_logout",
            "position" => "sidebar",
        ]);

        $accountNavigationService->put([
            "name" => "frontend_account_details",
            "order" => 1,
            "icon" => "inserat_erstellen",
            "path" => "frontend_account_details",
            "position" => "sidebar",
        ]);

        $accountNavigationService->put([
            "name" => "not_found",
            "order" => 1,
            "icon" => "inserat_melden",
            "path" => "not_found",
            "position" => "sidebar",
        ]);

        $accountNavigationService->put([
            "name" => "inserat_melden",
            "order" => 1,
            "icon" => "inserat_melden",
            "path" => "not_found",
            "position" => "sidebar",
        ]);

        $accountNavigationService->put([
            "name" => "eigenschaften",
            "order" => 1,
            "icon" => "eigenschaften",
            "path" => "not_found",
            "position" => "sidebar",
        ]);

        $accountNavigationService->put([
            "name" => "facebook",
            "order" => 1,
            "icon" => "facebook",
            "path" => "not_found",
            "position" => "sidebar",
        ]);

        $accountNavigationService->put([
            "name" => "kleinanzeigen",
            "order" => 1,
            "icon" => "kleinanzeigen",
            "path" => "not_found",
            "position" => "sidebar",
        ]);

        $accountNavigationService->put([
            "name" => "kostenlos",
            "order" => 1,
            "icon" => "kostenlos",
            "path" => "not_found",
            "position" => "sidebar",
        ]);

        $accountNavigationService->put([
            "name" => "merkliste",
            "order" => 1,
            "icon" => "merkliste",
            "path" => "not_found",
            "position" => "sidebar",
        ]);

        $accountNavigationService->put([
            "name" => "merken",
            "order" => 1,
            "icon" => "merken",
            "path" => "not_found",
            "position" => "sidebar",
        ]);

        $accountNavigationService->put([
            "name" => "top_inserat",
            "order" => 1,
            "icon" => "top_inserat",
            "path" => "not_found",
            "position" => "sidebar",
        ]);

    }
    
    /**
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     * @throws \Oforge\Engine\Modules\Core\Exceptions\ServiceNotFoundException
     */
    public function deactivate() {
        /** @var MiddlewareService $middlewareService */
        $middlewareService = Oforge()->Services()->get('middleware');
        $middlewareService->deactivate('frontend');
    }

    public function uninstall() {

    }
}
