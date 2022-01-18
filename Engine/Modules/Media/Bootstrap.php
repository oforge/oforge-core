<?php

namespace Oforge\Engine\Modules\Media;

use Exception;
use Oforge\Engine\Modules\AdminBackend\Core\Services\BackendNavigationService;
use Oforge\Engine\Modules\Core\Abstracts\AbstractBootstrap;
use Oforge\Engine\Modules\Core\Models\Config\ConfigType;
use Oforge\Engine\Modules\Core\Services\ConfigService;
use Oforge\Engine\Modules\I18n\Helper\I18N;
use Oforge\Engine\Modules\Media\Models\Media;
use Oforge\Engine\Modules\Media\Services\ImageCompressService;
use Oforge\Engine\Modules\Media\Services\MediaService;
use Oforge\Engine\Modules\Media\Twig\MediaExtension;
use Oforge\Engine\Modules\TemplateEngine\Core\Services\TemplateRenderService;

/**
 * Class Bootstrap
 *
 * @package Oforge\Engine\Modules\Media
 */
class Bootstrap extends AbstractBootstrap
{

    public function __construct()
    {
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

        $this->setConfiguration(
            'settingGroups',
            [
                [
                    'name'  => 'media',
                    'label' => [
                        'en' => 'Media',
                        'de' => 'Medien',
                    ],
                    'items' => [
                        [
                            'name'     => 'media_upload_image_adjustment_enabled',
                            'type'     => ConfigType::BOOLEAN,
                            'default'  => false,
                            'label'    => [
                                'en' => 'Image upload: Adjustments when uploading?',
                                'de' => 'Bildupload: Anpassungen beim hochladen?',
                            ],
                            'required' => false,
                        ],# media_upload_image_adjustment_enabled
                        [
                            'name'     => 'media_upload_image_adjustment_downscaling_max_width',
                            'type'     => ConfigType::INTEGER,
                            'default'  => 0,
                            'label'    => [
                                'en' => 'Image upload: Down scaling to max width (deactivated if 0)',
                                'de' => 'Bildupload: Verkleinern auf maximale Breite (deaktiviert wenn 0)',
                            ],
                            'required' => false,
                        ],# media_upload_image_adjustment_downscaling_max_width
                        [
                            'name'     => 'media_upload_image_adjustment_compress',
                            'type'     => ConfigType::BOOLEAN,
                            'default'  => false,
                            'label'    => [
                                'en' => 'Image upload: Compress?',
                                'de' => 'Bildupload: Komprimieren?',
                            ],
                            'required' => false,
                        ],# media_upload_image_adjustment_compress
                        [
                            'name'     => 'media_image_upscaling_enabled',
                            'type'     => ConfigType::BOOLEAN,
                            'default'  => true,
                            'label'    => [
                                'en' => 'Image upscaling (of small images) ?',
                                'de' => 'Bild-Upscaling (von kleinen Bildern)?',
                            ],
                            'required' => false,
                        ],# media_upscaling_enabled
                    ],
                ],# media
            ]#
        );
    }

    public function load()
    {
        EventHandler\MediaImageEvent::register();
        parent::load();
    }

    /** @inheritDoc */
    public function uninstall(bool $keepData)
    {
        if ( !$keepData) {
            $this->uninstallSettings();
        }
    }

    /** @inheritDoc */
    public function install()
    {
        $this->installSettings();
    }

    /** @inheritDoc */
    public function activate()
    {
        /** @var TemplateRenderService $templateRenderer */
        $templateRenderer = Oforge()->Services()->get('template.render');
        $templateRenderer->View()->addExtension(new MediaExtension());
        /** @var BackendNavigationService $backendNavigationService */
        $backendNavigationService = Oforge()->Services()->get('backend.navigation');
        $backendNavigationService->add(
            [
                'name'     => 'module_media',
                'order'    => 3,
                'position' => 'sidebar',
            ]#
        );
        $backendNavigationService->add(
            [
                'name'     => 'module_media_media',
                'parent'   => 'module_media',
                'icon'     => 'fa fa-picture-o',
                'path'     => 'backend_media',
                'position' => 'sidebar',
                'order'    => 1,
            ]#
        );
    }

    /**
     *
     */
    protected function installSettings()
    {
        try {
            /** @var ConfigService $configService */
            $configService = Oforge()->Services()->get('config');
            foreach ($this->getConfiguration('settingGroups') as $settingGroup) {
                I18N::translate('config_group_' . $settingGroup['name'], $settingGroup['label']);
                foreach ($settingGroup['items'] as $setting) {
                    $labelKey = 'config_' . $setting['name'];
                    I18N::translate($labelKey, $setting['label']);
                    $setting['label'] = $labelKey;
                    $setting['group'] = $settingGroup['name'];
                    $configService->add($setting);
                }
            }
        } catch (Exception $exception) {
            Oforge()->Logger()->logException($exception);
        }
    }

    /**
     *
     */
    protected function uninstallSettings()
    {
        try {
            /** @var ConfigService $configService */
            $configService = Oforge()->Services()->get('config');
            foreach ($this->getConfiguration('settingGroups') as $settingGroup) {
                foreach ($settingGroup['items'] as $setting) {
                    $configService->remove($setting['name']);
                }
            }
        } catch (Exception $exception) {
            Oforge()->Logger()->logException($exception);
        }
    }

}
