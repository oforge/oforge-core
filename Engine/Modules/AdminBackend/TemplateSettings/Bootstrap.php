<?php

namespace Oforge\Engine\Modules\AdminBackend\TemplateSettings;

use Oforge\Engine\Modules\AdminBackend\Core\Services\BackendNavigationService;
use Oforge\Engine\Modules\Core\Abstracts\AbstractBootstrap;
use Oforge\Engine\Modules\AdminBackend\TemplateSettings\Controller\Backend\TemplateSettingsController;

class Bootstrap extends AbstractBootstrap {
    /**
     * Bootstrap constructor.
     */
    public function __construct() {
        $this->endpoints = [
            "/backend/templates[/]" => [
                "controller" => TemplateSettingsController::class,
                "name" => "backend_template_settings",
                "asset_scope" => "Backend"
            ]
        ];
    }

    /**
     * @throws \Oforge\Engine\Modules\Core\Exceptions\ServiceNotFoundException
     */
    public function activate() {
        /** @var $backendNavigation BackendNavigationService */
        $backendNavigation = Oforge()->Services()->get("backend.navigation");

        $backendNavigation->put([
            "name"   => "backend_template_settings",
            "order"  => 98,
            "parent" => "admin",
            "icon"   => "fa fa-paint-brush",
            "path"   => "backend_template_settings",
            "position" => "sidebar",
        ]);
    }
}
