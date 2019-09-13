<?php

namespace Oforge\Engine\Modules\Core;

use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use Oforge\Engine\Modules\Core\Abstracts\AbstractBootstrap;
use Oforge\Engine\Modules\Core\Controller\Frontend\NotFoundController;
use Oforge\Engine\Modules\Core\Controller\Frontend\ServerErrorController;
use Oforge\Engine\Modules\Core\Models\Config\Config;
use Oforge\Engine\Modules\Core\Models\Config\ConfigType;
use Oforge\Engine\Modules\Core\Models\Config\Value;
use Oforge\Engine\Modules\Core\Models\Endpoint\Endpoint;
use Oforge\Engine\Modules\Core\Models\Event\EventModel;
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
use Oforge\Engine\Modules\I18n\Helper\I18N;

/**
 * Class Core-Bootstrap
 *
 * @package Oforge\Engine\Modules\Core
 */
class Bootstrap extends AbstractBootstrap {

    public function __construct() {
        $this->endpoints = [
            NotFoundController::class,
            ServerErrorController::class,
        ];

        $this->models = [
            Config::class,
            Endpoint::class,
            EventModel::class,
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
     * @throws Exceptions\ConfigOptionKeyNotExistException
     * @throws Exceptions\ServiceNotFoundException
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function install() {

        // TODO: translations won't work here, since this module is pre-loaded before anything else

        // I18N::translate('config_system_project_name',[
        //     'en' => 'Project name',
        //     'de' => 'Projektname',
        // ]);
        // I18N::translate('config_system_project_short',[
        //     'en' => 'Project short name',
        //     'de' => 'Projektname kurzform',
        // ]);
        // I18N::translate('config_system_project_copyright',[
        //     'en' => 'Project Copyright',
        //     'de' => 'Projekt Copyright',
        // ]);
        // I18N::translate('config_system_domain_name',[
        //     'en' => 'Domain name',
        //     'de' => 'Domain Name',
        // ]);
        // I18N::translate('config_debug_mode',[
        //     'en' => 'Debug mode',
        //     'de' => 'Debug Modus',
        // ]);
        // I18N::translate('debug_console',[
        //     'en' => 'Console output',
        //     'de' => 'Debug Konsole',
        // ]);
        // I18N::translate('config_debug_session',[
        //     'en' => 'Include session data',
        //     'de' => 'Zeige Sitzungsdaten',
        // ]);

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
            'name'     => 'system_project_domain_name',
            'type'     => ConfigType::STRING,
            'group'    => 'system',
            'default'  => '',
            'label'    => 'config_system_domain_name',
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
        $configService->add([
            'name'    => 'css_source_map',
            'type'    => ConfigType::BOOLEAN,
            'group'   => 'debug',
            'default' => true,
            'label'   => 'config_css_source_map',
        ]);
        $configService->add([
            'name'     => 'system_format_datetime',
            'type'     => ConfigType::STRING,
            'group'    => 'date_format',
            'default'  => 'd.m.Y H:i:s',
            'label'    => 'config_system_format_datetime',
            'required' => true,
        ]);
        $configService->add([
            'name'     => 'system_format_date',
            'type'     => ConfigType::STRING,
            'group'    => 'date_format',
            'default'  => 'd.m.Y',
            'label'    => 'config_system_format_date',
            'required' => true,
        ]);
        $configService->add([
            'name'     => 'system_format_time',
            'type'     => ConfigType::STRING,
            'group'    => 'date_format',
            'default'  => 'H:i:s',
            'label'    => 'config_system_format_time',
            'required' => true,
        ]);
        $configService->add([
            'name'     => 'system_datetimepicker_format_datetime',
            'type'     => ConfigType::STRING,
            'group'    => 'date_format',
            'default'  => 'DD.MM.YYYY HH:mm',
            'label'    => 'config_system_format_datetime',
            'required' => true,
        ]);
        $configService->add([
            'name'     => 'system_datetimepicker_format_date',
            'type'     => ConfigType::STRING,
            'group'    => 'date_format',
            'default'  => 'DD.MM.YYYY',
            'label'    => 'config_system_format_date',
            'required' => true,
        ]);
        $configService->add([
            'name'     => 'system_datetimepicker_format_time',
            'type'     => ConfigType::STRING,
            'group'    => 'date_format',
            'default'  => 'HH:mm',
            'label'    => 'config_system_format_time',
            'required' => true,
        ]);
    }

}
