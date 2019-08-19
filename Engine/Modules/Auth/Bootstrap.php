<?php

namespace Oforge\Engine\Modules\Auth;

use Oforge\Engine\Modules\Auth\Models\User\BackendUser;
use Oforge\Engine\Modules\Auth\Models\User\BackendUserDetail;
use Oforge\Engine\Modules\Auth\Services\AuthService;
use Oforge\Engine\Modules\Auth\Services\BackendLoginService;
use Oforge\Engine\Modules\Auth\Services\PasswordService;
use Oforge\Engine\Modules\Auth\Services\PermissionService;
use Oforge\Engine\Modules\Core\Abstracts\AbstractBootstrap;

/**
 * Class Bootstrap
 *
 * @package Oforge\Engine\Modules\Auth
 */
class Bootstrap extends AbstractBootstrap {

    /**
     * Bootstrap constructor.
     */
    public function __construct() {
        $this->models = [
            BackendUser::class,
            BackendUserDetail::class,
        ];

        $this->services = [
            'auth'          => AuthService::class,
            'backend.login' => BackendLoginService::class,
            'password'      => PasswordService::class,
            'permissions'   => PermissionService::class,
        ];
    }

}
