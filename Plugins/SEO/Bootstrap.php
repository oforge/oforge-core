<?php

namespace Seo;

use Oforge\Engine\Modules\CMS\Services\ContentTypeManagementService;
use Oforge\Engine\Modules\Core\Abstracts\AbstractBootstrap;
use Oforge\Engine\Modules\TemplateEngine\Core\Services\TemplateRenderService;
use Seo\Middleware\SeoMiddleware;
use Seo\Models\SeoUrl;
use Seo\Services\SeoService;
use Seo\Twig\SeoExtension;

/**
 * Class Bootstrap
 *
 * @package Messenger
 */
class Bootstrap extends AbstractBootstrap {

    /**
     * Bootstrap constructor.
     */
    public function __construct() {
        $this->models = [
            SeoUrl::class,
        ];

        $this->services = [
            "seo" => SeoService::class,
        ];

    }

    protected $order = 0;

    public function load() {
        Oforge()->App()->add(new SeoMiddleware());

        /**
         * @var $templateRenderer TemplateRenderService
         */
        $templateRenderer = Oforge()->Services()->get("template.render");

        $templateRenderer->View()->addExtension(new SeoExtension());
    }
}
