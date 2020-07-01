<?php

namespace SocialLogin;

use Oforge\Engine\Modules\Core\Abstracts\AbstractBootstrap;
use SocialLogin\Models\LoginProvider;
use SocialLogin\Models\SocialLogin;
use SocialLogin\Services\LoginConnectService;
use SocialLogin\Services\LoginProviderService;
use SocialLogin\Services\UserLoginService;

/**
 * Class Bootstrap
 *
 * @package Messenger
 */
class Bootstrap extends AbstractBootstrap {

    /**
     * Bootstrap constructor.
     */
    public function __construct() {
        $this->endpoints = [
            Controller\Frontend\SocialLoginController::class,
        ];

        $this->models = [
            SocialLogin::class,
            LoginProvider::class,
        ];

        $this->services = [
            'sociallogin.providers' => LoginProviderService::class,
            'sociallogin.login'     => UserLoginService::class,
            'sociallogin'           => LoginConnectService::class,
        ];

        $this->dependencies = [
            \FrontendUserManagement\Bootstrap::class,
        ];

        $this->order = 0;
    }

}
