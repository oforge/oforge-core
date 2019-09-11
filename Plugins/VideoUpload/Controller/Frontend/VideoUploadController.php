<?php

namespace VideoUpload\Controller\Frontend;

use Oforge\Engine\Modules\Core\Annotation\Endpoint\EndpointAction;
use Oforge\Engine\Modules\Core\Annotation\Endpoint\EndpointClass;
use Oforge\Engine\Modules\Core\Exceptions\ServiceNotFoundException;
use Oforge\Engine\Modules\I18n\Helper\I18N;
use Slim\Http\Request;
use Slim\Http\Response;
use Vimeo\Vimeo;

/**
 * Class VideoUploadController
 *
 * @package VideoUpload\Controller\Frontend
 * @EndpointClass(path="/account/newsletter", name="frontend_account_newsletter", assetScope="Frontend")
 */
class VideoUploadController
{
    /**
     * @EndpointAction()
     */
    public function indexAction(Request $request, Response $response) {
    }
    public function testAction(Request $request, Response $response) {
        /* Example code from vimeo

        $client = new Vimeo("{client_id}", "{client_secret}", "{access_token}");
        $response = $client->request('/tutorial', array(), 'GET');
        print_r($response);

        */
    }
}
