<?php


namespace ImageUpload\Controller\Frontend;

use FrontendUserManagement\Abstracts\SecureFrontendController;
use Oforge\Engine\Modules\Core\Annotation\Endpoint\EndpointAction;
use Oforge\Engine\Modules\Core\Annotation\Endpoint\EndpointClass;
use Slim\Http\Request;
use Slim\Http\Response;

/**
 * Class VideoUploadController
 *
 * @package VideoUpload\Controller\Frontend
 * @EndpointClass(path="/image-upload", name="imageUpload", assetScope="Frontend")
 */
class ImageUploadController extends SecureFrontendController
{
    /**
     * @param Request $request
     * @param Response $response
     * @return void
     * @EndpointAction()
     */
    public function indexAction(Request $request, Response $response) {
        $file = $request->getUploadedFiles();


        // TODO: Create MediaObject and save Image
        $response->withStatus(200);
    }
}
