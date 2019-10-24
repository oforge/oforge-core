<?php

namespace VideoUpload\Controller\Frontend;

use Oforge\Engine\Modules\Core\Annotation\Endpoint\EndpointAction;
use Oforge\Engine\Modules\Core\Annotation\Endpoint\EndpointClass;
use Oforge\Engine\Modules\Core\Exceptions\ServiceNotFoundException;
use Oforge\Engine\Modules\Core\Models\Config\Config;
use Oforge\Engine\Modules\Core\Services\ConfigService;
use Oforge\Engine\Modules\I18n\Helper\I18N;
use Slim\Http\Request;
use Slim\Http\Response;
use VideoUpload\Models\VideoKey;
use VideoUpload\Services\VideoUploadService;
use Vimeo\Exceptions\VimeoRequestException;
use Vimeo\Vimeo;

/**
 * Class VideoUploadController
 *
 * @package VideoUpload\Controller\Frontend
 * @EndpointClass(path="/vimeo-api", name="vimeoApi", assetScope="Frontend")
 */
class VideoUploadController
{
    /**
     * @param Request $request
     * @param Response $response
     * @EndpointAction()
     */
    public function indexAction(Request $request, Response $response) {
    }

    public function credentialsAction(Request $request, Response $response) {
        $configService = Oforge()->Services()->get('config');
        /** @var Config[] $groupConfigs */
        $groupConfigs = $configService->getGroupConfigs('vimeo');
        foreach ($groupConfigs as $config) {
            $vimeoGroupConfigs[$config->getName()] = $config->getValues()[0]->getValue();
        }
        if (isset($vimeoGroupConfigs) && !empty($vimeoGroupConfigs)) {
            Oforge()->View()->assign(['json' => $vimeoGroupConfigs]);
        }
    }

    /**
     * @param Request $request
     * @param Response $response
     * @param $args
     * @throws ServiceNotFoundException
     * @EndpointAction(path="/id/{id}")
     */
    public function getVideoKeyAction(Request $request, Response $response, $args){
        $insertionId = $args['id'];
        $jsonResponse = [];

        /** @var VideoUploadService $videoUploadService */
        $videoUploadService = Oforge()->Services()->get('video.upload');

        /** @var VideoKey $videoKey */
        $videoKey = $videoUploadService->getVideoKey($insertionId);

        if($videoKey !== null) {
            $jsonResponse['vimeo_video_key'] = $videoKey->getVideoKey();

            Oforge()->View()->assign(['json' => $jsonResponse]);
        }
    }
}
