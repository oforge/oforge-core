<?php

namespace Oforge\Engine\Modules\AdminBackendDocumentation;

use Oforge\Engine\Modules\AdminBackend\Services\SidebarNavigationService;
use Oforge\Engine\Modules\AdminBackendDocumentation\Controller\Backend\DocumentationUIController;
use Oforge\Engine\Modules\Core\Abstracts\AbstractBootstrap;

class Bootstrap extends AbstractBootstrap
{
    /**
     * Bootstrap constructor.
     */
    public function __construct()
    {
        $this->endpoints = [
            "/backend/documentation/ui" => ["controller" => DocumentationUIController::class, "name" => "backend_documentation_ui", "asset_scope" => "Backend"]
        ];
    }

    /**
     *
     */
    public function install()
    {
        /**
         * @var $sidebarNavigation SidebarNavigationService
         */
        $sidebarNavigation = Oforge()->Services()->get("backend.sidebar.navigation");

        $sidebarNavigation->put([
            "name" => "backend_documentation",
            "order" => 100
        ]);

        $sidebarNavigation->put([
            "name" => "backend_ui_elements",
            "order" => 100,
            "parent" => "backend_documentation",
            "icon" => "fa fa-laptop"
        ]);

        $sidebarNavigation->put([
            "name" => "backend_documentation_ui_general",
            "order" => 1,
            "parent" => "backend_ui_elements",
            "icon" => "fa fa-circle-o",
            "path" => "backend_documentation_ui_general"
        ]);

        $sidebarNavigation->put([
            "name" => "backend_documentation_ui_icons",
            "order" => 2,
            "parent" => "backend_ui_elements",
            "icon" => "fa fa-circle-o",
            "path" => "backend_documentation_ui_icons"
        ]);

        $sidebarNavigation->put([
            "name" => "backend_documentation_ui_buttons",
            "order" => 3,
            "parent" => "backend_ui_elements",
            "icon" => "fa fa-circle-o",
            "path" => "backend_documentation_ui_buttons"
        ]);

        $sidebarNavigation->put([
            "name" => "backend_documentation_ui_sliders",
            "order" => 4,
            "parent" => "backend_ui_elements",
            "icon" => "fa fa-circle-o",
            "path" => "backend_documentation_ui_sliders"
        ]);

        $sidebarNavigation->put([
            "name" => "backend_documentation_ui_timeline",
            "order" => 5,
            "parent" => "backend_ui_elements",
            "icon" => "fa fa-circle-o",
            "path" => "backend_documentation_ui_timeline"
        ]);

        $sidebarNavigation->put([
            "name" => "backend_documentation_ui_modals",
            "order" => 6,
            "parent" => "backend_ui_elements",
            "icon" => "fa fa-circle-o",
            "path" => "backend_documentation_ui_modals"
        ]);

    }
}
