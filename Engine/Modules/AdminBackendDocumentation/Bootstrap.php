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

    }
}
