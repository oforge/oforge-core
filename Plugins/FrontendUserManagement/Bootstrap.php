<?php

namespace FrontendUserManagement;

use FrontendUserManagement\Models\User;
use Oforge\Engine\Modules\Core\Abstracts\AbstractBootstrap;

class Bootstrap extends AbstractBootstrap {
    public function __construct() {
        $this->endpoints = [
            "/login" => ["controller" => Controller\Frontend\LoginController::class, "name" => "frontend_login"],
            "/logout" => ["controller" => Controller\Frontend\LogoutController::class, "name" => "frontend_logout"],
            "/register" => ["controller" => Controller\Frontend\RegistrationController::class, "name" => "frontend_registration"],
            "/forgot-password" => ["controller" => Controller\Frontend\ForgotPasswordController::class, "name" => "frontend_forgot_password"]
        ];
        
        $this->models = [
            User::class
        ];
        
        $this->services = [
            'frontend.user.management.password.reset' => Services\PasswordResetService::class,
            'frontend.user.management.login' => Services\FrontendUserLoginService::class
        ];
    }
    
    public function install() {
    }
}
