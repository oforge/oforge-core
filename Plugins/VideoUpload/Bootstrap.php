<?php

namespace VideoUpload;

use Oforge\Engine\Modules\Core\Abstracts\AbstractBootstrap;
use Oforge\Engine\Modules\Core\Models\Config\ConfigType;
use Oforge\Engine\Modules\Core\Services\ConfigService;
use VideoUpload\Controller\Frontend\VideoUploadController;

/**
 * Class Bootstrap
 *
 * @package VideoUpload
 */
class Bootstrap extends AbstractBootstrap {

    /*
     *  'client_id' => '1096cac8f6676464e317bb98be0be54d23c61deb',
        'client_secret' => 'CiVTC7r6uRznTtWX3erWP3XuYAO2MiNI69ZliAa7TwMV1eLSv7TQ19SukG5ua9sK2H5AkY21yTr5s/Cy4Z83mSxPi1ihqxO+bpEoP90G8//1E6mI39zBfDxEAJ+zLej2',
        'access_token' => 'cf13093b3e1c21db56d3adb152f12882'
     */

    public function __construct() {
        $this->dependencies = [
            \Insertion\Bootstrap::class,
        ];

        $this->endpoints = [
            VideoUploadController::class,
        ];

        $this->models = [];

        $this->services = [];
    }

    public function install() {
        /** @var ConfigService $configService */
        $configService = Oforge()->Services()->get('config');

        $configService->add([
            'name'     => 'vimeo_client_id',
            'type'     => ConfigType::STRING,
            'group'    => 'vimeo',
            'default'  => '',
            'value'  => '1096cac8f6676464e317bb98be0be54d23c61deb',
            'label'    => 'vimeo_client_id',
            'required' => true,
            'order'    => 0,
        ]);
        $configService->add([
            'name'     => 'vimeo_client_secret',
            'type'     => ConfigType::STRING,
            'group'    => 'vimeo',
            'default'  => '',
            'value'    => 'CiVTC7r6uRznTtWX3erWP3XuYAO2MiNI69ZliAa7TwMV1eLSv7TQ19SukG5ua9sK2H5AkY21yTr5s/Cy4Z83mSxPi1ihqxO+bpEoP90G8//1E6mI39zBfDxEAJ+zLej2',
            'label'    => 'vimeo_client_secret',
            'required' => true,
            'order'    => 1,
        ]);
        $configService->add([
            'name'     => 'vimeo_access_token',
            'type'     => ConfigType::STRING,
            'group'    => 'vimeo',
            'default'  => '',
            'value'    => '98cc11b80aa5107bc60f38cb31f944c9',
            'label'    => 'vimeo_access_token',
            'required' => true,
            'order'    => 2,
        ]);
    }
}
