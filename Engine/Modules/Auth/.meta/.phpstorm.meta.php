<?php

namespace PHPSTORM_META {

    if (function_exists('override')) {
        override(\Oforge\Engine\Modules\Core\Manager\Services\ServiceManager::get(0), map([
            'auth'          => \Oforge\Engine\Modules\Auth\Services\AuthService::class,
            'backend.login' => \Oforge\Engine\Modules\Auth\Services\BackendLoginService::class,
            'password'      => \Oforge\Engine\Modules\Auth\Services\PasswordService::class,
            'permissions'   => \Oforge\Engine\Modules\Auth\Services\PermissionService::class,
        ]));

        override(\Oforge\Engine\Modules\Core\Services\ConfigService::get(0), map([
            'auth_core_password_min_length' => 'int',
        ]));

    }

}
