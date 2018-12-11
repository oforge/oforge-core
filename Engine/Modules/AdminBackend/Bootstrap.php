<?php

namespace Oforge\Engine\Modules\AdminBackend;

use Oforge\Engine\Modules\AdminBackend\Controller\Backend\DashboardController;
use Oforge\Engine\Modules\AdminBackend\Controller\Backend\IndexController;
use Oforge\Engine\Modules\AdminBackend\Controller\Backend\LoginController;
use Oforge\Engine\Modules\AdminBackend\Services\SidebarNavigation;
use Oforge\Engine\Modules\Core\Abstracts\AbstractBootstrap;
use Oforge\Engine\Modules\Core\Services\ConfigService;
use Oforge\Engine\Modules\TemplateEngine\Services\TemplateRenderService;


class Bootstrap extends AbstractBootstrap
{
    /**
     * Bootstrap constructor.
     */
    public function __construct()
    {
        $this->services = [
          "backend.sidebar.navigation" => SidebarNavigation::class
        ];

        $this->endpoints = [
            "/backend" => ["controller" => IndexController::class, "name" => "backend", "asset_scope" => "Backend"],
            "/backend/login" => ["controller" => LoginController::class, "name" => "backend_login", "asset_scope" => "Backend"],
            "/backend/dashboard" => ["controller" => DashboardController::class, "name" => "backend_dashboard", "asset_scope" => "Backend"]
        ];
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
            "default" => "Oforge"
        ]);

        $configService->add([
            "name" => "backend_project_short",
            "label" => "Projektkürzel",
            "type" => "string",
            "required" => true,
            "default" => "OF"
        ]);


        /**
         * @var $templateRender TemplateRenderService
         */
        $templateRender = Oforge()->Services()->get("template.render");

        $templateRender->View()->getEnvironment()->addExtension(TwigConfigAccess);

    }
}
