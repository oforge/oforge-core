<?php

namespace PHPSTORM_META {

    if (function_exists('override')) {
        override(\Oforge\Engine\Modules\Core\Manager\Services\ServiceManager::get(0), map([
            'config'             => \Oforge\Engine\Modules\Core\Services\ConfigService::class,
            'endpoint'           => \Oforge\Engine\Modules\Core\Services\EndpointService::class,
            'middleware'         => \Oforge\Engine\Modules\Core\Services\MiddlewareService::class,
            'ping'               => \Oforge\Engine\Modules\Core\Services\PingService::class,
            'plugin.access'      => \Oforge\Engine\Modules\Core\Services\PluginAccessService::class,
            'plugin.state'       => \Oforge\Engine\Modules\Core\Services\PluginStateService::class,
            'redirect'           => \Oforge\Engine\Modules\Core\Services\RedirectService::class,
            'session.management' => \Oforge\Engine\Modules\Core\Services\Session\SessionManagementService::class,
            'store.keyvalue'     => \Oforge\Engine\Modules\Core\Services\KeyValueStoreService::class,
        ]));
    }

}
