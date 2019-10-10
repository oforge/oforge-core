<?php

namespace VideoUpload;

use Oforge\Engine\Modules\Core\Abstracts\AbstractBootstrap;
use Oforge\Engine\Modules\Core\Models\Config\ConfigType;
use Oforge\Engine\Modules\Core\Services\ConfigService;
use VideoUpload\Controller\Frontend\VideoUploadController;
use VideoUpload\Middleware\VideoUploadMiddleware;
use VideoUpload\Models\VideoKey;
use VideoUpload\Services\VideoUploadService;

/**
 * Class Bootstrap
 *
 * @package VideoUpload
 */
class Bootstrap extends AbstractBootstrap
{

    public function __construct()
    {
        $this->endpoints = [
            VideoUploadController::class,
        ];

        $this->models = [
            VideoKey::class
        ];

        $this->services = [
            'video.upload' => VideoUploadService::class
        ];

        /*
        $this->middlewares = [
            'insertions_processSteps' => [
                'class' => VideoUploadMiddleware::class,
                'position' => 1,
            ],
        ];
        */

    }

    public function install()
    {
        /** @var ConfigService $configService */
        $configService = Oforge()->Services()->get('config');

        $configService->add([
            'name' => 'vimeo_client_id',
            'type' => ConfigType::STRING,
            'group' => 'vimeo',
            'default' => '',
            'label' => 'vimeo_client_id',
            'required' => true,
            'order' => 0,
        ]);
        $configService->add([
            'name' => 'vimeo_client_secret',
            'type' => ConfigType::STRING,
            'group' => 'vimeo',
            'default' => '',
            'label' => 'vimeo_client_secret',
            'required' => true,
            'order' => 1,
        ]);
        $configService->add([
            'name' => 'vimeo_access_token',
            'type' => ConfigType::STRING,
            'group' => 'vimeo',
            'default' => '',
            'label' => 'vimeo_access_token',
            'required' => true,
            'order' => 2,
        ]);
    }
}
