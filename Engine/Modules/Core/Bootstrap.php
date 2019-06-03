<?php

namespace Oforge\Engine\Modules\Core;

use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use Oforge\Engine\Modules\Core\Abstracts\AbstractBootstrap;
use Oforge\Engine\Modules\Core\Controller\Frontend\NotFoundController;
use Oforge\Engine\Modules\Core\Models\Config\Config;
use Oforge\Engine\Modules\Core\Models\Config\ConfigType;
use Oforge\Engine\Modules\Core\Models\Config\Value;
use Oforge\Engine\Modules\Core\Models\Endpoint\Endpoint;
use Oforge\Engine\Modules\Core\Models\Module\Module;
use Oforge\Engine\Modules\Core\Models\Plugin\Middleware;
use Oforge\Engine\Modules\Core\Models\Plugin\Plugin;
use Oforge\Engine\Modules\Core\Models\Store\KeyValue;
use Oforge\Engine\Modules\Core\Services\ConfigService;
use Oforge\Engine\Modules\Core\Services\EncryptionService;
use Oforge\Engine\Modules\Core\Services\EndpointService;
use Oforge\Engine\Modules\Core\Services\KeyValueStoreService;
use Oforge\Engine\Modules\Core\Services\MiddlewareService;
use Oforge\Engine\Modules\Core\Services\PingService;
use Oforge\Engine\Modules\Core\Services\PluginAccessService;
use Oforge\Engine\Modules\Core\Services\PluginStateService;
use Oforge\Engine\Modules\Core\Services\RedirectService;
use Oforge\Engine\Modules\Core\Services\Session\SessionManagementService;

/**
 * Class Core-Bootstrap
 *
 * @package Oforge\Engine\Modules\Core
 */
class Bootstrap extends AbstractBootstrap {

    public function __construct() {
        $this->endpoints = [
            NotFoundController::class,
        ];

        $this->models = [
            Config::class,
            Endpoint::class,
            KeyValue::class,
            Middleware::class,
            Module::class,
            Plugin::class,
            Value::class,
        ];

        $this->services = [
            'config'             => ConfigService::class,
            'encryption'         => EncryptionService::class,
            'endpoint'           => EndpointService::class,
            'middleware'         => MiddlewareService::class,
            'ping'               => PingService::class,
            'plugin.access'      => PluginAccessService::class,
            'plugin.state'       => PluginStateService::class,
            'redirect'           => RedirectService::class,
            'session.management' => SessionManagementService::class,
            'store.keyvalue'     => KeyValueStoreService::class,
        ];

        $this->order = 0;
    }

    /**
     * @throws Exceptions\ConfigElementAlreadyExistException
     * @throws Exceptions\ConfigOptionKeyNotExistException
     * @throws Exceptions\ServiceNotFoundException
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function install() {
        //TODO in import csv
        // I18N::translate('config_system_project_name', 'Project name', 'en');
        // I18N::translate('config_system_project_short', 'Project short name', 'en');
        // I18N::translate('config_debug_mode', 'Debug mode', 'en');
        // I18N::translate('debug_console', 'Console output', 'en');
        // I18N::translate('config_debug_session', 'Include session data', 'en');
        /** @var ConfigService $configService */
        $configService = Oforge()->Services()->get('config');
        $configService->add([
            'name'     => 'system_project_name',
            'type'     => ConfigType::STRING,
            'group'    => 'system',
            'default'  => 'Oforge',
            'label'    => 'config_system_project_name',
            'required' => true,
        ]);
        $configService->add([
            'name'     => 'system_project_short',
            'type'     => ConfigType::STRING,
            'group'    => 'system',
            'default'  => 'OF',
            'label'    => 'config_system_project_short',
            'required' => true,
        ]);
        $configService->add([
            'name'     => 'system_project_copyright',
            'type'     => ConfigType::STRING,
            'group'    => 'system',
            'default'  => 'Oforge',
            'label'    => 'config_system_project_copyright',
            'required' => true,
        ]);
        $configService->add([
            'name'    => 'debug_mode',
            'type'    => ConfigType::BOOLEAN,
            'group'   => 'debug',
            'default' => false,
            'label'   => 'config_debug_mode',
        ]);
        $configService->add([
            'name'    => 'debug_console',
            'type'    => ConfigType::BOOLEAN,
            'group'   => 'debug',
            'default' => true,
            'label'   => 'debug_console',
        ]);
        $configService->add([
            'name'    => 'debug_session',
            'type'    => ConfigType::BOOLEAN,
            'group'   => 'debug',
            'default' => true,
            'label'   => 'config_debug_session',
        ]);
    }

}
