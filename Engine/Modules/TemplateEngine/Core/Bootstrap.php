<?php

namespace Oforge\Engine\Modules\TemplateEngine\Core;

use Oforge\Engine\Modules\Core\Abstracts\AbstractBootstrap;
use Oforge\Engine\Modules\TemplateEngine\Core\Manager\TemplateManager;
use Oforge\Engine\Modules\TemplateEngine\Core\Manager\ViewManager;
use Oforge\Engine\Modules\TemplateEngine\Core\Middleware\AssetsMiddleware;
use Oforge\Engine\Modules\TemplateEngine\Core\Models\ScssVariable;
use Oforge\Engine\Modules\TemplateEngine\Core\Models\Template\Template;
use Oforge\Engine\Modules\TemplateEngine\Core\Services\CssAssetService;
use Oforge\Engine\Modules\TemplateEngine\Core\Services\JsAssetService;
use Oforge\Engine\Modules\TemplateEngine\Core\Services\ScssVariableService;
use Oforge\Engine\Modules\TemplateEngine\Core\Services\StaticAssetService;
use Oforge\Engine\Modules\TemplateEngine\Core\Services\TemplateAssetService;
use Oforge\Engine\Modules\TemplateEngine\Core\Services\TemplateManagementService;
use Oforge\Engine\Modules\TemplateEngine\Core\Services\TemplateRenderService;

class Bootstrap extends AbstractBootstrap {
    /**
     * Bootstrap constructor.
     */
    public function __construct() {
        $this->services = [
            "scss.variables"      => ScssVariableService::class,
            "template.render"     => TemplateRenderService::class,
            "template.management" => TemplateManagementService::class,
            "assets.template"     => TemplateAssetService::class,
            "assets.js"           => JsAssetService::class,
            "assets.css"          => CssAssetService::class,
            "assets.static"       => StaticAssetService::class,
        ];

        $this->models = [
            Template::class,
            ScssVariable::class,
        ];

        $this->middleware = [
            "*" => ["class" => AssetsMiddleware::class, "position" => 0],
        ];

        $this->order = 1;

        Oforge()->setTemplateManager(TemplateManager::getInstance());
        Oforge()->setViewManager(ViewManager::getInstance());
    }

    /**
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     * @throws \Oforge\Engine\Modules\Core\Exceptions\ServiceNotFoundException
     * @throws \Oforge\Engine\Modules\Core\Exceptions\TemplateNotFoundException
     */
    public function activate() {
        Oforge()->Templates()->init();

        /** @var TemplateManagementService $templateManagementService */
        $templateManagementService = Oforge()->Services()->get("template.management");

        $templateName = $templateManagementService->getActiveTemplate()->getName();

        $scopes = ["Frontend", "Backend"];

        foreach ($scopes as $scope) {
            if (!Oforge()->Services()->get("assets.css")->isBuild($scope)) {
                Oforge()->Services()->get("assets.template")->build($templateName, $scope);
            }
        }
    }
}
