<?php

namespace FrontendUserManagement;

use FrontendUserManagement\Middleware\FrontendSecureMiddleware;
use FrontendUserManagement\Models\User;
use FrontendUserManagement\Services\FrontendSecureMiddlewareService;
use Oforge\Engine\Modules\Core\Abstracts\AbstractBootstrap;

class Bootstrap extends AbstractBootstrap {
    public function __construct() {
        $this->endpoints = [
            "/login" => ["controller" => Controller\Frontend\LoginController::class, "name" => "frontend_login"],
            "/login-registration" => ["controller" => Controller\Frontend\LoginRegistrationController::class, "name" => "frontend_login_registration"],
            "/logout" => ["controller" => Controller\Frontend\LogoutController::class, "name" => "frontend_logout"],
            "/register" => ["controller" => Controller\Frontend\RegistrationController::class, "name" => "frontend_registration"],
            "/forgot-password" => ["controller" => Controller\Frontend\ForgotPasswordController::class, "name" => "frontend_forgot_password"],
            "/profile" => ["controller" => Controller\Frontend\ProfileController::class, "name" => "frontend_profile"],
        ];
        
        $this->middleware = [
            "frontend_profile" => ["class" => FrontendSecureMiddleware::class, "position" => 1]
        ];
        
        $this->models = [
            User::class
        ];
        
        $this->services = [
            'frontend.user.management.password.reset' => Services\PasswordResetService::class,
            'frontend.user.management.login' => Services\FrontendUserLoginService::class,
            'frontend.user.management.registration' => Services\RegistrationService::class,
            'frontend.secure.middleware' => Services\FrontendSecureMiddlewareService::class,
            'password.reset' => Services\PasswordResetService::class
        ];
    }
    
    /**
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     * @throws \Oforge\Engine\Modules\Core\Exceptions\ServiceNotFoundException
     */
    public function activate() {
        /** @var FrontendSecureMiddlewareService $frontendSecureMiddlewareService */
        $frontendSecureMiddlewareService = Oforge()->Services()->get('frontend.secure.middleware');
        $frontendSecureMiddlewareService->activate('frontend_profile');
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
