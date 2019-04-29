<?php

namespace Oforge\Engine\Modules\AdminBackend\Plugins;

use Oforge\Engine\Modules\AdminBackend\Core\Services\BackendNavigationService;
use Oforge\Engine\Modules\AdminBackend\Plugins\Controller\Backend\PluginController;
use Oforge\Engine\Modules\Core\Abstracts\AbstractBootstrap;

/**
 * Class Bootstrap
 *
 * @package Oforge\Engine\Modules\AdminBackend\Plugins
 */
class Bootstrap extends AbstractBootstrap {

    /**
     * Bootstrap constructor.
     */
    public function __construct() {
        $this->endpoints = [
            PluginController::class,
        ];
    }

    /**
     *
     */
    public function install() {
        /** @var BackendNavigationService $sidebarNavigation */
        $sidebarNavigation = Oforge()->Services()->get('backend.navigation');
        $sidebarNavigation->put([
            'name'     => 'backend_plugins',
            'order'    => 1,
            'parent'   => 'admin',
            'icon'     => 'fa fa-plug',
            'path'     => 'backend_plugins',
            'position' => 'sidebar',
        ]);
    }

}
