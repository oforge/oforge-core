<?php 

namespace Oforge\Engine\Modules\Media;

use Oforge\Engine\Modules\Core\Abstracts\AbstractBootstrap;

use Oforge\Engine\Modules\Media\Models\Media;
use Oforge\Engine\Modules\Media\Services\ImageCompressService;
use Oforge\Engine\Modules\Media\Services\MediaService;
use Oforge\Engine\Modules\Media\Twig\MediaExtension;
use Oforge\Engine\Modules\TemplateEngine\Core\Services\TemplateRenderService;

class Bootstrap extends AbstractBootstrap {

    public function __construct() {
        $this->models = [
            Media::class
        ];

        $this->services = [
            "media" => MediaService::class,
            "image.compress" => ImageCompressService::class
        ];
    }

    /**
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     * @throws \Oforge\Engine\Modules\Core\Exceptions\ServiceNotFoundException
     * @throws \Oforge\Engine\Modules\Core\Exceptions\TemplateNotFoundException
     * @throws \Twig_Error_Loader
     */
    public function activate()
    {
        /**
         * @var $templateRenderer TemplateRenderService
         */
        $templateRenderer = Oforge()->Services()->get("template.render");

        $templateRenderer->View()->addExtension(new MediaExtension());
    }
}
