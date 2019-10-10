<?php


namespace VideoUpload\Middleware;


use Slim\Http\Request;
use Slim\Http\Response;
use VideoUpload\Services\VideoUploadService;

class VideoUploadMiddleware
{
    public function prepend(Request $request, Response $response) {
        Oforge()->View()->assign(['blubblub' => 'blub']);

        /**
         * @var VideoUploadService $videoUploadService
         */
        //$videoUploadService = Oforge()->Services()->get('video.upload');

        /*
        if(isset($data['insertion']['vimeo_video_id'])) {
            $videoUploadService->updateOrCreateVideoKey(intval($insertionId), $data['insertion']['vimeo_video_id']);
        }
        */

        /**
         * @var VideoUploadService $videoUploadService
         */
        /*
        $videoUploadService = Oforge()->Services()->get('video.upload');
        $videoKey = $videoUploadService->getVideoKey(1);

        Oforge()->View()->assign([
            "video_id" => $videoKey,]);
        */
    }

}
