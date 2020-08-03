<?php

namespace Oforge\Engine\Modules\Media;

use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use Oforge\Engine\Modules\AdminBackend\Core\Services\BackendNavigationService;
use Oforge\Engine\Modules\Core\Abstracts\AbstractBootstrap;
use Oforge\Engine\Modules\Core\Exceptions\ServiceNotFoundException;
use Oforge\Engine\Modules\Core\Exceptions\Template\TemplateNotFoundException;
use Oforge\Engine\Modules\Media\Models\Media;
use Oforge\Engine\Modules\Media\Services\ImageCompressService;
use Oforge\Engine\Modules\Media\Services\MediaService;
use Oforge\Engine\Modules\Media\Twig\MediaExtension;
use Oforge\Engine\Modules\TemplateEngine\Core\Services\TemplateRenderService;
use Twig_Error_Loader;

/**
 * Class Bootstrap
 *
 * @package Oforge\Engine\Modules\Media
 */
class Bootstrap extends AbstractBootstrap {

    public function __construct() {
        $this->endpoints = [
            Controller\Backend\Media\AjaxController::class,
            Controller\Backend\Media\MediaController::class,
            Controller\Frontend\Media\MediaController::class,
        ];

        $this->models = [
            Media::class,
        ];

        $this->services = [
            'media'          => MediaService::class,
            'image.compress' => ImageCompressService::class,
        ];
    }

    /** @inheritDoc */
    public function activate() {
        /** @var TemplateRenderService $templateRenderer */
        $templateRenderer = Oforge()->Services()->get('template.render');
        $templateRenderer->View()->addExtension(new MediaExtension());
        /** @var BackendNavigationService $backendNavigationService */
        $backendNavigationService = Oforge()->Services()->get('backend.navigation');
        $backendNavigationService->add([
            'name'     => 'module_media',
            'order'    => 3,
            'position' => 'sidebar',
        ]);
        $backendNavigationService->add([
            'name'     => 'module_media_media',
            'parent'   => 'module_media',
            'icon'     => 'fa fa-picture-o',
            'path'     => 'backend_media',
            'position' => 'sidebar',
            'order'    => 1,
        ]);
    }
}
