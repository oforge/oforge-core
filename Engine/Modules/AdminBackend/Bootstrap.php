<?php

namespace Oforge\Engine\Modules\AdminBackend;

use Oforge\Engine\Modules\AdminBackend\Controller\Backend\DashboardController;
use Oforge\Engine\Modules\AdminBackend\Controller\Backend\IndexController;
use Oforge\Engine\Modules\AdminBackend\Controller\Backend\LoginController;
use Oforge\Engine\Modules\AdminBackend\Controller\Backend\LogoutController;
use Oforge\Engine\Modules\AdminBackend\Middleware\BackendSecureMiddleware;
use Oforge\Engine\Modules\AdminBackend\Models\BackendNavigation;
use Oforge\Engine\Modules\AdminBackend\Services\Permissions;
use Oforge\Engine\Modules\AdminBackend\Services\BackendNavigationService;
use Oforge\Engine\Modules\Core\Abstracts\AbstractBootstrap;
use Oforge\Engine\Modules\Core\Services\ConfigService;


class Bootstrap extends AbstractBootstrap
{
    /**
     * Bootstrap constructor.
     */
    public function __construct()
    {
        $this->services = [
            "backend.navigation" => BackendNavigationService::class,
            "permissions" => Permissions::class
        ];

        $this->endpoints = [
            "/backend[/]" => ["controller" => IndexController::class, "name" => "backend", "asset_scope" => "Backend"],
            "/backend/login" => ["controller" => LoginController::class, "name" => "backend_login", "asset_scope" => "Backend"],
            "/backend/logout" => ["controller" => LogoutController::class, "name" => "backend_logout", "asset_scope" => "Backend"],
            "/backend/dashboard" => ["controller" => DashboardController::class, "name" => "backend_dashboard", "asset_scope" => "Backend"]
        ];

        $this->middleware = [
            "*" => ["class" => BackendSecureMiddleware::class, "position" => 1]
        ];

        $this->models = [
            BackendNavigation::class
        ];

        $this->order = 2;
    }

    /**
     *
     */
    public function install()
    {
        /**
         * @var $configService ConfigService
         */
        $configService = Oforge()->Services()->get("config");

        $configService->add([
            "name" => "backend_project_name",
            "label" => "Projektname",
            "type" => "string",
            "required" => true,
            "default" => "Oforge",
            "group" => "backend"
        ]);

        $configService->add([
            "name" => "backend_project_short",
            "label" => "Projektkürzel",
            "type" => "string",
            "required" => true,
            "default" => "OF",
            "group" => "backend"
        ]);

        /**
         * @var $sidebarNavigation BackendNavigationService
         */
        $sidebarNavigation = Oforge()->Services()->get("backend.navigation");

        $sidebarNavigation->put([
            "name" => "admin",
            "order" => 99
        ]);

        /*
        $sidebarNavigation->put([
            "name" => "help",
            "order" => 99,
            "parent" => "admin",
            "icon" => "ion-help"
        ]);

        $sidebarNavigation->put([
            "name" => "ionicons",
            "order" => 2,
            "parent" => "help",
            "icon" => "ion-nuclear",
            "path" => "backend_dashboard_ionicons"
        ]);

        $sidebarNavigation->put([
            "name" => "fontAwesome",
            "order" => 1,
            "parent" => "help",
            "icon" => "fa fa-fort-awesome",
            "path" => "backend_dashboard_fontAwesome"
        ]);
        */

    }
}
