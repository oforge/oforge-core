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
     * @EndpointAction()
     */
    public function indexAction(Request $request, Response $response) {
    }

    /**
     * @param Request $request
     * @param Response $response
     *
     * @return void
     * @throws ServiceNotFoundException
     * @throws VimeoRequestException
     * @throws \Doctrine\ORM\ORMException
     * @EndpointAction()
     */
    public function testAction(Request $request, Response $response) {
        /** @var ConfigService $configService */
        $configService = Oforge()->Services()->get('config');

        /** @var Config[] $groupConfigs */
        $groupConfigs = $configService->getGroupConfigs('vimeo');
        $vimeoGroupConfigs = [];
        foreach ($groupConfigs as $config) {
            $vimeoGroupConfigs[$config->getName()] = $config->getValues()[0]->getValue();
        }
        if (isset($vimeoGroupConfigs) && !empty($vimeoGroupConfigs)) {
            $client = new Vimeo($vimeoGroupConfigs['vimeo_client_id'], $vimeoGroupConfigs['vimeo_client_secret'], $vimeoGroupConfigs['vimeo_access_token']);

            //$apiResponse = $client->upload(ROOT_PATH . '/var/public/videos/Horses_3.mp4');

            //$apiResponse = $client->request('/tutorial', array(), 'GET');
            //Oforge()->View()->assign(['json' => $apiResponse]);
        }
    }
}
