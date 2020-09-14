<?php

namespace PHPSTORM_META {

    if (function_exists('override')) {
        /**
         * Services
         */
        // override(\Oforge\Engine\Modules\Core\Manager\Services\ServiceManager::get(0), map([
        // ]));
        /**
         * Events
         */
        override(\Oforge\Engine\Modules\Core\Manager\Events\Event::create(0), map([
            'insertion.middleware.profileProgress.checkOther'    => '',
            'insertion.middleware.profileProgress.dataKeys' => '',
            'insertion.middleware.profileProgress.isset'    => '',
        ]));
        override(\Oforge\Engine\Modules\Core\Manager\Events\EventManager::attach(0), map([
            'insertion.middleware.profileProgress.checkOther'    => '',
            'insertion.middleware.profileProgress.dataKeys' => '',
            'insertion.middleware.profileProgress.isset'    => '',
        ]));
    }

}
