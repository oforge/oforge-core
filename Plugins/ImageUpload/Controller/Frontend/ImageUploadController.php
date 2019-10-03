<?php

namespace ImageUpload\Controller\Frontend;

use Doctrine\ORM\ORMException;
use FrontendUserManagement\Abstracts\SecureFrontendController;
use Oforge\Engine\Modules\Core\Annotation\Endpoint\EndpointAction;
use Oforge\Engine\Modules\Core\Annotation\Endpoint\EndpointClass;
use Oforge\Engine\Modules\Core\Exceptions\ServiceNotFoundException;
use Oforge\Engine\Modules\Media\Services\MediaService;
use Slim\Http\Request;
use Slim\Http\Response;

/**
 * Class VideoUploadController
 *
 * @package VideoUpload\Controller\Frontend
 * @EndpointClass(path="/image-upload", name="imageUpload", assetScope="Frontend")
 */
class ImageUploadController extends SecureFrontendController {
    /**
     * @param Request $request
     * @param Response $response
     *
     * @return Response
     * @EndpointAction()
     */
    public function indexAction(Request $request, Response $response) {
        $file = [];
        foreach ($_FILES['files'] as $key => $value) {
            $file[$key] = $value[0];
        }

        $allowedTypes = [
            'image/jpeg',
            'image/jpg',
            'image/png',
        ];

        if (!(isset($file) && $file['error'] == 0)) {
            return $response->withStatus(400);
        } elseif (!in_array($file['type'], $allowedTypes)) {
            return $response->withStatus(415);
        }

        /** @var MediaService $mediaService */
        try {
            $mediaService = Oforge()->Services()->get('media');
            $media     = $mediaService->add($file);
            $imageData = $media->toArray();
        } catch (ServiceNotFoundException $e) {
            return $response->withStatus(500);
        } catch (ORMException $e) {
            return $response->withStatus(500);
        }

        return $response->withBody(["imageData" => json_encode($imageData)])->withStatus(200);
    }
}
