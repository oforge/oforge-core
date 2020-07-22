<?php

namespace ImageUpload\Controller\Frontend;

use Doctrine\ORM\ORMException;
use FrontendUserManagement\Abstracts\SecureFrontendController;
use FrontendUserManagement\Services\FrontendUserService;
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
        if ($request->isPost()) {
            $file = [];
            foreach ($_FILES['files'] as $key => $value) {
                $file[$key] = $value[0];
            }

            if (!(isset($file) && $file['error'] == 0)) {
                return $response->withStatus(400);
            }

            $allowedTypes = [
                'image/jpeg',
                'image/jpg',
                'image/png',
            ];

            if (!in_array($file['type'], $allowedTypes)) {
                return $response->withStatus(415);
            }

            try {
                /**
                 * @var $userService FrontendUserService
                 */
                $userService = Oforge()->Services()->get('frontend.user');
                $user        = $userService->getUser();

                /** @var MediaService $mediaService */
                $mediaService = Oforge()->Services()->get('media');
                $media        = $mediaService->add($file, null, $user == null ? null : $user->getId());
                $imageData    = $media->toArray();
            } catch (ServiceNotFoundException $e) {
                return $response->withStatus(500);
            } catch (ORMException $e) {
                return $response->withStatus(500);
            }
            Oforge()->View()->assign(['json' => ['imageData' => $imageData]]);

            //return $response->withJson(['imageData' => $imageData], 200);
            return $response->withStatus(200);
        }

        return $response->withStatus(400);
    }

    /**
     * @param Request $request
     * @param Response $response
     *
     * @return Response
     * @EndpointAction()
     */
    public function rotateAction(Request $request, Response $response) {
        if ($request->isGet()) {
            $id   = $_GET["id"];
            $path = $_GET["path"];

            /** @var MediaService $mediaService */
            $mediaService = Oforge()->Services()->get('media');

            $media = $mediaService->getById($id);

            if ($media->getPath() == $path) {

                $mediaService->rotate($id, 90);
            }
        }

        return $response->withStatus(403);
    }
}
