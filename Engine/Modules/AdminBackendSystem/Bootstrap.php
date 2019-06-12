<?php

namespace Oforge\Engine\Modules\AdminBackendSystem;

use Oforge\Engine\Modules\AdminBackend\Middleware\BackendSecureMiddleware;
use Oforge\Engine\Modules\AdminBackend\Services\SidebarNavigationService;
use Oforge\Engine\Modules\AdminBackendSystem\Controller\Backend\System\RoutesController;
use Oforge\Engine\Modules\Core\Abstracts\AbstractBootstrap;

class Bootstrap extends AbstractBootstrap
{
    public function __construct()
    {
        $this->endpoints = [
            "/backend/system/routes" => ["controller" => RoutesController::class, "name" => "backend_system_routes", "asset_scope" => "Backend"],
        ];

        $this->middleware = [
            "*" => ["class" => BackendSecureMiddleware::class, "position" => 1]
        ];

    }

    /**
     * @throws \Oforge\Engine\Modules\Core\Exceptions\ServiceNotFoundException
     */

    public function activate()
    {
        /**
         * @var $sidebarNavigation SidebarNavigationService
         */
        $sidebarNavigation = Oforge()->Services()->get("backend.sidebar.navigation");


        $sidebarNavigation->put([
            "name" => "admin",
            "order" => 99
        ]);
        $sidebarNavigation->put([
            "name" => "system",
            "order" => 99,
            "parent" => "admin",
            "icon" => "ion-clipboard"
        ]);
        $sidebarNavigation->put([
            "name" => "routes",
            "order" => 1,
            "parent" => "system",
            "icon" => "ion-ios-flag",
            "path" => "backend_system_routes"
        ]);
    }
}