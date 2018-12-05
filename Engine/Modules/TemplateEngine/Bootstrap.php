<?php
namespace Oforge\Engine\Modules\TemplateEngine;

use Oforge\Engine\Modules\Core\Abstracts\AbstractBootstrap;
use Oforge\Engine\Modules\TemplateEngine\Manager\TemplateManager;
use Oforge\Engine\Modules\TemplateEngine\Manager\ViewManager;
use Oforge\Engine\Modules\TemplateEngine\Middleware\AssetsMiddleware;
use Oforge\Engine\Modules\TemplateEngine\Models\Template\Template;
use Oforge\Engine\Modules\TemplateEngine\Services\CssAssetService;
use Oforge\Engine\Modules\TemplateEngine\Services\JsAssetService;
use Oforge\Engine\Modules\TemplateEngine\Services\TemplateAssetService;
use Oforge\Engine\Modules\TemplateEngine\Services\TemplateManagementService;
use Oforge\Engine\Modules\TemplateEngine\Services\TemplateRenderService;

class Bootstrap extends AbstractBootstrap {
    /**
     * Bootstrap constructor.
     */
    public function __construct() {
        $this->services = [
            "template.renderer" => TemplateRenderService::class,
            "template.management" => TemplateManagementService::class,
            "assets.template" => TemplateAssetService::class,
            "assets.js" => JsAssetService::class,
            "assets.css" => CssAssetService::class,
        ];
        
        $this->models = [
            Template::class
        ];

        $this->middleware = [
            "*" => ["class" => AssetsMiddleware::class, "position" => 0]
        ];

        Oforge()->setTemplateManager(TemplateManager::getInstance());
        Oforge()->setViewManager(ViewManager::getInstance());
    }
}
