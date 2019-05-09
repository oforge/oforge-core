<?php

namespace Oforge\Engine\Modules\Core;

use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use Oforge\Engine\Modules\Core\Abstracts\AbstractBootstrap;
use Oforge\Engine\Modules\Core\Controller\Frontend\NotFoundController;
use Oforge\Engine\Modules\Core\Models\Config\Element;
use Oforge\Engine\Modules\Core\Models\Config\Value;
use Oforge\Engine\Modules\Core\Models\Endpoint\Endpoint;
use Oforge\Engine\Modules\Core\Models\Module\Module;
use Oforge\Engine\Modules\Core\Models\Plugin\Middleware;
use Oforge\Engine\Modules\Core\Models\Plugin\Plugin;
use Oforge\Engine\Modules\Core\Models\Store\KeyValue;
use Oforge\Engine\Modules\Core\Services\ConfigService;
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
            Module::class,
            Element::class,
            Value::class,
            Plugin::class,
            Middleware::class,
            Endpoint::class,
            KeyValue::class,
        ];

        $this->services = [
            'config'             => ConfigService::class,
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
        /** @var ConfigService $configService */
        $configService = Oforge()->Services()->get('config');
        $configService->update([
            'name'    => 'system_debug',
            'label'   => 'Debug aktivieren',
            'type'    => 'boolean',
            'default' => true,
            'group'   => 'system',
        ]);
        $configService->update([
            'name'    => 'session_debug',
            'label'   => 'session_debug',
            'type'    => 'boolean',
            'default' => false,
            'group'   => 'system',
        ]);
    }
}
