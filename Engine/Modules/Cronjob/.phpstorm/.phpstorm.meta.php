<?php

namespace PHPSTORM_META {

    if (function_exists('override')) {
        override(\Oforge\Engine\Modules\Core\Manager\Services\ServiceManager::get(0), map([
            'cronjob' => \Oforge\Engine\Modules\Cronjob\Services\CronjobService::class,
        ]));
    }

}
