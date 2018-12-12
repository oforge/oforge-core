<?php
namespace Oforge\Engine\Modules\TemplateExtensions;

use Oforge\Engine\Modules\Core\Abstracts\AbstractBootstrap;
use Oforge\Engine\Modules\TemplateEngine\Services\TemplateRenderService;
use Oforge\Engine\Modules\TemplateExtensions\Twig\AccessConfigExtension;
use Oforge\Engine\Modules\TemplateExtensions\Twig\BackendExtension;
use Oforge\Engine\Modules\TemplateExtensions\Twig\SlimExtension;
use Oforge\Engine\Modules\TemplateExtensions\Twig\TokenExtension;

class Bootstrap extends AbstractBootstrap {
    /**
     * Bootstrap constructor.
     */
    public function __construct() {

    }
    
    /**
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     * @throws \Oforge\Engine\Modules\Core\Exceptions\ServiceNotFoundException
     * @throws \Twig_Error_Loader
     */
    public function activate()
    {
        /**
         * @var $templateRenderer TemplateRenderService
         */
        $templateRenderer = Oforge()->Services()->get("template.render");

        $templateRenderer->View()->addExtension(new AccessConfigExtension());
        $templateRenderer->View()->addExtension(new SlimExtension());
        $templateRenderer->View()->addExtension(new BackendExtension());
        $templateRenderer->View()->addExtension(new TokenExtension());
    }
}
