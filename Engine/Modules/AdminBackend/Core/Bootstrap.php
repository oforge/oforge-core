<?php

namespace Oforge\Engine\Modules\AdminBackend\Core;

use Oforge\Engine\Modules\AdminBackend\Core\Controller\Backend\DashboardController;
use Oforge\Engine\Modules\AdminBackend\Core\Controller\Backend\IndexController;
use Oforge\Engine\Modules\AdminBackend\Core\Controller\Backend\LoginController;
use Oforge\Engine\Modules\AdminBackend\Core\Controller\Backend\LogoutController;
use Oforge\Engine\Modules\AdminBackend\Core\Controller\Backend\FavoritesController;
use Oforge\Engine\Modules\AdminBackend\Core\Middleware\BackendSecureMiddleware;
use Oforge\Engine\Modules\AdminBackend\Core\Models\BackendNavigation;
use Oforge\Engine\Modules\AdminBackend\Core\Models\BackendUserFavorites;
use Oforge\Engine\Modules\AdminBackend\Core\Models\DashboardWidget;
use Oforge\Engine\Modules\AdminBackend\Core\Models\UserDashboardWidgets;
use Oforge\Engine\Modules\AdminBackend\Core\Services\BackendNavigationService;
use Oforge\Engine\Modules\AdminBackend\Core\Services\DashboardWidgetsService;
use Oforge\Engine\Modules\AdminBackend\Core\Services\UserFavoritesService;
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
            "backend.favorites" => UserFavoritesService::class,
            "backend.dashboard.widgets" => DashboardWidgetsService::class
       ];

        $this->endpoints = [
            "/backend[/]" => ["controller" => IndexController::class, "name" => "backend", "asset_scope" => "Backend"],
            "/backend/login" => ["controller" => LoginController::class, "name" => "backend_login", "asset_scope" => "Backend"],
            "/backend/logout" => ["controller" => LogoutController::class, "name" => "backend_logout", "asset_scope" => "Backend"],
            "/backend/dashboard" => ["controller" => DashboardController::class, "name" => "backend_dashboard", "asset_scope" => "Backend"],
            "/backend/favorites" => ["controller" => FavoritesController::class, "name" => "backend_favorites", "asset_scope" => "Backend"]
        ];

        $this->middlewares = [
            "backend" => ["class" => BackendSecureMiddleware::class, "position" => 1]
        ];

        $this->models = [
            BackendNavigation::class,
            BackendUserFavorites::class,
            DashboardWidget::class,
            UserDashboardWidgets::class
        ];

        $this->order = 3;
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

        $configService->add([
            "name" => "backend_project_copyright",
            "label" => "Copyright",
            "type" => "string",
            "required" => true,
            "default" => "Oforge",
            "group" => "backend"
        ]);

        $configService->add([
            "name" => "backend_project_footer_text",
            "label" => "Footer Text",
            "type" => "string",
            "required" => true,
            "default" => "Oforge",
            "group" => "backend"
        ]);


        /**
         * @var $sidebarNavigation BackendNavigationService
         */
        $sidebarNavigation = Oforge()->Services()->get("backend.navigation");

        $sidebarNavigation->put([
            "name" => "admin",
            "order" => 99,
            "position" => "sidebar",
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
