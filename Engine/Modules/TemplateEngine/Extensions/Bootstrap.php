<?php

namespace Oforge\Engine\Modules\TemplateEngine\Extensions;

use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use Oforge\Engine\Modules\Core\Abstracts\AbstractBootstrap;
use Oforge\Engine\Modules\Core\Exceptions\ServiceNotFoundException;
use Oforge\Engine\Modules\Core\Exceptions\Template\TemplateNotFoundException;
use Oforge\Engine\Modules\TemplateEngine\Core\Services\TemplateRenderService;
use Oforge\Engine\Modules\TemplateEngine\Extensions\Services\UrlService;
use Oforge\Engine\Modules\TemplateEngine\Extensions\Twig\AccessExtension;
use Oforge\Engine\Modules\TemplateEngine\Extensions\Twig\BackendExtension;
use Oforge\Engine\Modules\TemplateEngine\Extensions\Twig\SlimExtension;
use Oforge\Engine\Modules\TemplateEngine\Extensions\Twig\TokenExtension;
use Twig_Error_Loader;

/**
 * Class Bootstrap
 *
 * @package Oforge\Engine\Modules\TemplateEngine\Extensions
 */
class Bootstrap extends AbstractBootstrap {
    /**
     * Bootstrap constructor.
     */
    public function __construct() {
        $this->services = [
            'url' => UrlService::class
        ];
    }

    /**
     * @throws ORMException
     * @throws OptimisticLockException
     * @throws ServiceNotFoundException
     * @throws TemplateNotFoundException
     * @throws Twig_Error_Loader
     */
    public function activate() {
        /** @var TemplateRenderService $templateRenderer */
        $templateRenderer = Oforge()->Services()->get('template.render');

        $templateRenderer->View()->addExtension(new AccessExtension());
        $templateRenderer->View()->addExtension(new SlimExtension());
        $templateRenderer->View()->addExtension(new BackendExtension());
        $templateRenderer->View()->addExtension(new TokenExtension());
    }

}
