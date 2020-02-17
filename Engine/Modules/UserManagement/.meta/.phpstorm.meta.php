<?php

namespace PHPSTORM_META {

    if (function_exists('override')) {
        override(\Oforge\Engine\Modules\Core\Manager\Services\ServiceManager::get(0), map([
            'backend.user'       => \Oforge\Engine\Modules\UserManagement\Services\BackendUserService::class,
            'backend.users.crud' => \Oforge\Engine\Modules\UserManagement\Services\BackendUsersCrudService::class,
        ]));
    }

}
