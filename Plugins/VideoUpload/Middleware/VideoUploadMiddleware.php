<?php


namespace VideoUpload\Middleware;

use Slim\Http\Request;
use Slim\Http\Response;;
use Slim\Route;
use VideoUpload\Services\VideoUploadService;
use \Oforge\Engine\Modules\Core\Exceptions\ServiceNotFoundException;
use \Doctrine\ORM\ORMException;

class VideoUploadMiddleware
{
    public function prepend(Request $request, Response $response)
    {
        $typeId = $this->getTypeId($request);

        if (isset($_SESSION['insertion' . $typeId])) {
            $data                           = $_SESSION['insertion' . $typeId]['insertion'];
            $videoKey                       = $data['vimeo_video_id'];
            $_SESSION['vimeo_video_key']    = $videoKey;
        }
    }

    /**
     * @param Request $request
     * @param Response $response
     * @throws ORMException
     * @throws ServiceNotFoundException
     */
    public function append (Request $request, Response $response) {
        if (isset($_SESSION['insertion_id']) && isset($_SESSION['vimeo_video_key'])) {
            $videoKey = $_SESSION['vimeo_video_key'];
            $insertionId = $_SESSION['insertion_id'];

            /**
             * @var VideoUploadService $videoUploadService
             */
            $videoUploadService = Oforge()->Services()->get('video.upload');

            $videoUploadService->updateOrCreateVideoKey(intval($insertionId), $videoKey);

            unset($_SESSION['vimeo_video_key']);
            unset($_SESSION['insertion_id']);
        }
    }

    public function getTypeId (Request $request) {
        /** @var Route $route*/
        $route      = $request->getAttribute('route');
        $arguments  = $route->getArguments();
        $typeId     = $arguments ['type'];
        return $typeId;
    }
}
