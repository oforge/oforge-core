<?php

namespace SocialLogin;

use FrontendUserManagement\Models\User;
use FrontendUserManagement\Services\UserService;
use Oforge\Engine\Modules\Core\Abstracts\AbstractBootstrap;
use Oforge\Engine\Modules\Core\Manager\Events\Event;
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

    public function load() {
        Oforge()->Events()->attach(User::class . '::delete', Event::SYNC, function (Event $event) {
            $data = $event->getData();
            /**
             * @var  $loginService UserLoginService
             */
            $loginService = Oforge()->Services()->get('sociallogin.login');

            /** @var UserService $userService */
            $userService = Oforge()->Services()->get('frontend.user.management.user');

            $user = $userService->getUserById($data["id"]);
            if ($user != null) {
                $loginService->deleteUser($user);
            }
        });
    }
}
