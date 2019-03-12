<?php

namespace FrontendUserManagement;

use FrontendUserManagement\Middleware\FrontendSecureMiddleware;
use FrontendUserManagement\Middleware\FrontendUserStateMiddleware;
use FrontendUserManagement\Models\ProfileNavigation;
use FrontendUserManagement\Models\User;
use FrontendUserManagement\Services\FrontendSecureMiddlewareService;
use FrontendUserManagement\Services\ProfileNavigationService;
use Oforge\Engine\Modules\Core\Abstracts\AbstractBootstrap;
use Oforge\Engine\Modules\Core\Models\Plugin\Middleware;

class Bootstrap extends AbstractBootstrap {
    public function __construct() {
        $this->endpoints = [
            "/login" => ["controller" => Controller\Frontend\LoginController::class, "name" => "frontend_login"],
            "/login-registration" => ["controller" => Controller\Frontend\LoginRegistrationController::class, "name" => "frontend_login_registration"],
            "/logout" => ["controller" => Controller\Frontend\LogoutController::class, "name" => "frontend_logout"],
            "/registration" => ["controller" => Controller\Frontend\RegistrationController::class, "name" => "frontend_registration"],
            "/forgot-password" => ["controller" => Controller\Frontend\ForgotPasswordController::class, "name" => "frontend_forgot_password"],
            "/profile" => ["controller" => Controller\Frontend\ProfileController::class, "name" => "frontend_profile"],
        ];
        
        $this->middleware = [
            "frontend_profile" => ["class" => FrontendSecureMiddleware::class, "position" => 1],
            "frontend" => ["class" => FrontendUserStateMiddleware::class, "position" => 1]
        ];
        
        $this->models = [
            ProfileNavigation::class,
            User::class
        ];
        
        $this->services = [
            'frontend.user.management.password.reset' => Services\PasswordResetService::class,
            'frontend.user.management.login' => Services\FrontendUserLoginService::class,
            'frontend.user.management.registration' => Services\RegistrationService::class,
            'frontend.secure.middleware' => Services\FrontendSecureMiddlewareService::class,
            'frontend.user.management.profile.navigation' => Services\ProfileNavigationService::class,
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
        /** @var FrontendSecureMiddlewareService $frontendSecureMiddlewareService */
        /** @var ProfileNavigationService $profileNavigationService */
        $frontendSecureMiddlewareService = Oforge()->Services()->get('frontend.secure.middleware');
        $frontendSecureMiddlewareService->activate('frontend_profile');

        $profileNavigationService = Oforge()->Services()->get('frontend.user.management.profile.navigation');
        $profileNavigationService->put([
            "name" => "frontend_logout",
            "order" => 1000,
            "icon" => "icon icon--logout",
            "path" => "frontend_logout",
            "position" => "sidebar",
        ]);
    }
    
    /**
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     * @throws \Oforge\Engine\Modules\Core\Exceptions\ServiceNotFoundException
     */
    public function deactivate() {
        /** @var FrontendSecureMiddlewareService $frontendSecureMiddlewareService */
        $frontendSecureMiddlewareService = Oforge()->Services()->get('frontend.secure.middleware');
        $frontendSecureMiddlewareService->deactivate('frontend_profile');
    }
}
