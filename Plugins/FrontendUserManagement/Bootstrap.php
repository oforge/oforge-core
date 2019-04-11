<?php

namespace FrontendUserManagement;

use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use FrontendUserManagement\Middleware\FrontendSecureMiddleware;
use FrontendUserManagement\Middleware\FrontendUserStateMiddleware;
use FrontendUserManagement\Middleware\AccountNavigationMiddleware;
use FrontendUserManagement\Models\AccountNavigation;
use FrontendUserManagement\Models\User;
use FrontendUserManagement\Models\UserAddress;
use FrontendUserManagement\Models\UserDetail;
use FrontendUserManagement\Services\AccountNavigationService;
use Oforge\Engine\Modules\Core\Abstracts\AbstractBootstrap;
use Oforge\Engine\Modules\Core\Exceptions\ConfigElementAlreadyExists;
use Oforge\Engine\Modules\Core\Exceptions\ConfigOptionKeyNotExists;
use Oforge\Engine\Modules\Core\Exceptions\ParentNotFoundException;
use Oforge\Engine\Modules\Core\Exceptions\ServiceNotFoundException;
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
        
        $this->middlewares = [
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
            'frontend.user.management.user.details' => Services\UserDetailsService::class,
            'password.reset' => Services\PasswordResetService::class
        ];
    }

}
