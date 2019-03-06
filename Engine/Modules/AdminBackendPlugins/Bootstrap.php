<?php

namespace Oforge\Engine\Modules\AdminBackendPlugins;

use Oforge\Engine\Modules\AdminBackend\Controller\Backend\LoginController;
use Oforge\Engine\Modules\AdminBackend\Services\BackendNavigationService;
use Oforge\Engine\Modules\AdminBackendPlugins\Controller\Backend\PluginController;
use Oforge\Engine\Modules\Core\Abstracts\AbstractBootstrap;


class Bootstrap extends AbstractBootstrap
{
    /**
     * Bootstrap constructor.
     */
    public function __construct()
    {
        $this->endpoints = [
            "/backend/plugins" => ["controller" => PluginController::class, "name" => "backend_plugins", "asset_scope" => "Backend"]
        ];
    }

    /**
     *
     */
    public function install()
    {
        /** @var $sidebarNavigation BackendNavigationService */
        $sidebarNavigation = Oforge()->Services()->get("backend.navigation");

        $sidebarNavigation->put([
            "name" => "backend_plugins",
            "order" => 1,
            "parent" => "admin",
            "icon" => "fa fa-plug",
            "path" => "backend_plugins",
            "position" => "sidebar",
        ]);
    }
}
