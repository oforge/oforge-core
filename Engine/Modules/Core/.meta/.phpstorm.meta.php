<?php

namespace PHPSTORM_META {

    if (function_exists('override')) {
        override(\Oforge\Engine\Modules\Core\Manager\Services\ServiceManager::get(0), map([
            'config'             => \Oforge\Engine\Modules\Core\Services\ConfigService::class,
            'encryption'         => \Oforge\Engine\Modules\Core\Services\EncryptionService::class,
            'endpoint'           => \Oforge\Engine\Modules\Core\Services\EndpointService::class,
            'middleware'         => \Oforge\Engine\Modules\Core\Services\MiddlewareService::class,
            'ping'               => \Oforge\Engine\Modules\Core\Services\PingService::class,
            'plugin.access'      => \Oforge\Engine\Modules\Core\Services\PluginAccessService::class,
            'plugin.state'       => \Oforge\Engine\Modules\Core\Services\PluginStateService::class,
            'redirect'           => \Oforge\Engine\Modules\Core\Services\RedirectService::class,
            'session.management' => \Oforge\Engine\Modules\Core\Services\Session\SessionManagementService::class,
            'store.keyvalue'     => \Oforge\Engine\Modules\Core\Services\KeyValueStoreService::class,
            'token'              => \Oforge\Engine\Modules\Core\Services\TokenService::class,
        ]));

        registerArgumentsSet('oforge_flash_message_types', 'success', 'error', 'warning', 'info');
        expectedArguments(\Oforge\Engine\Modules\TemplateEngine\Core\Twig\TwigFlash::addMessage(), 0, argumentsSet('oforge_flash_message_types'));
        expectedArguments(\Oforge\Engine\Modules\TemplateEngine\Core\Twig\TwigFlash::addExceptionMessage(), 0, argumentsSet('oforge_flash_message_types'));

        override(\Oforge\Engine\Modules\Core\Abstracts\AbstractBootstrap::getConfiguration(0), map([
            'backendDashboardWidgets' => '',
            'backendNavigations'      => '',
            'settingGroups'           => '',
            'twigExtensions'          => '',
        ]));
        override(\Oforge\Engine\Modules\Core\Abstracts\AbstractBootstrap::setConfiguration(0), map([
            'backendDashboardWidgets' => '',
            'backendNavigations'      => '',
            'settingGroups'           => '',
            'twigExtensions'          => '',
        ]));
    }

}
