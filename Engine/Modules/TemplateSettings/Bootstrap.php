<?php

namespace Oforge\Engine\Modules\TemplateSettings;

use Oforge\Engine\Modules\AdminBackend\Services\BackendNavigationService;
use Oforge\Engine\Modules\Core\Abstracts\AbstractBootstrap;
use Oforge\Engine\Modules\TemplateSettings\Controller\Backend\TemplateSettingsController;

class Bootstrap extends AbstractBootstrap
{
    /**
     * Bootstrap constructor.
     */
    public function __construct()
    {
        $this->endpoints = [
            "/backend/templates[/]" => ["controller" => TemplateSettingsController::class, "name" => "template_settings", "asset_scope" => "Backend"],
        ];
    }

    /**
     *
     */
    public function activate()
    {
        /**
         * @var $backendNavigation BackendNavigationService
         */

        $backendNavigation = Oforge()->Services()->get("backend.navigation");

        $backendNavigation->put([
            "name" => "template_settings",
            "order" => 98,
            "parent" => "admin",
            "icon" => "fa fa-paint-brush",
            "path" => "template_settings"
        ]);

    }
}
