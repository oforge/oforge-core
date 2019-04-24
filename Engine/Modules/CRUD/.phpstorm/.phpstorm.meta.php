<?php

namespace PHPSTORM_META {

    if (function_exists('override')) {
        override(\Oforge\Engine\Modules\Core\Manager\Services\ServiceManager::get(0), map([
            'crud' => \Oforge\Engine\Modules\CRUD\Services\GenericCrudService::class,
        ]));
    }

}
