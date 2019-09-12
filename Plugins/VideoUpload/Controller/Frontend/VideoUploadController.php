<?php

namespace VideoUpload\Controller\Frontend;

use Oforge\Engine\Modules\Core\Annotation\Endpoint\EndpointAction;
use Oforge\Engine\Modules\Core\Annotation\Endpoint\EndpointClass;
use Oforge\Engine\Modules\Core\Exceptions\ServiceNotFoundException;
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
     * @return Response
     * @throws VimeoRequestException
     * @EndpointAction()
     */
    public function testAction(Request $request, Response $response) {
        // TODO: Change vimeoSettings to key value store?!
        $vimeoSettings = Oforge()->Settings()->get('vimeo');
        $client = new Vimeo($vimeoSettings['client_id'], $vimeoSettings['client_secret'], $vimeoSettings['access_token']);
        $apiResponse = $client->request('/tutorial', array(), 'GET');
        Oforge()->View()->assign(['json' => $apiResponse]);
    }
}
