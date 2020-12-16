<?php

namespace Youtube\Controller\Frontend;

use Oforge\Engine\Modules\Core\Annotation\Endpoint\EndpointAction;
use Oforge\Engine\Modules\Core\Annotation\Endpoint\EndpointClass;
use Oforge\Engine\Modules\Core\Exceptions\ServiceNotFoundException;
use Slim\Http\Request;
use Slim\Http\Response;
use Youtube\Services\YoutubeService;

/**
 * Class YoutubeController
 *
 * @package Youtube\Controller\Frontend
 * @EndpointClass(path="/youtube-api", name="youtube", assetScope="Frontend")
 */
class YoutubeController {
    /**
     * @param Request $request
     * @param Response $response
     * @EndpointAction()
     */
    public function indexAction(Request $request, Response $response) {
    }

    /**
     * @param Request $request
     * @param Response $response
     *
     * @throws ServiceNotFoundException
     * @EndpointAction(path="/exists")
     */
    public function existsAction(Request $request, Response $response) {
        $videoID = $_GET['videoId'];
        /** @var YoutubeService $youtubeService */
        $youtubeService = Oforge()->Services()->get('video.youtube');
        $data           = $youtubeService->resolveVideoData($videoID);
        $jsonResponse   = ['exists' => false];
        if ($data != null) {
            $jsonResponse['exists']  = true;
            $jsonResponse['content'] = $data;
        }

        Oforge()->View()->assign(['json' => $jsonResponse]);
    }
}
