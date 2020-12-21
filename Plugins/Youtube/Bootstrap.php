<?php

namespace Youtube;

use Insertion\Models\Insertion;
use Oforge\Engine\Modules\Core\Abstracts\AbstractBootstrap;
use Oforge\Engine\Modules\Core\Helper\ArrayHelper;
use Oforge\Engine\Modules\Core\Manager\Events\Event;
use Oforge\Engine\Modules\I18n\Helper\I18N;
use Oforge\Engine\Modules\TemplateEngine\Core\Services\TemplateRenderService;
use Youtube\Controller\Frontend\YoutubeController;
use Youtube\Models\YoutubeVideo;
use Youtube\Services\YoutubeService;
use Youtube\Twig\YoutubeExtension;

/**
 * Class Bootstrap
 *
 * @package Youtube
 */
class Bootstrap extends AbstractBootstrap {

    public function __construct() {
        $this->endpoints = [
            YoutubeController::class,
        ];

        $this->models = [
            YoutubeVideo::class,
        ];

        $this->services = [
            'video.youtube' => YoutubeService::class,
        ];
    }

    public function load() {
        /**
         * @var $templateRenderer TemplateRenderService
         */
        $templateRenderer = Oforge()->Services()->get("template.render");

        $templateRenderer->View()->addExtension(new YoutubeExtension());

        Oforge()->Events()->attach(Insertion::class . '::created', Event::SYNC, function (Event $event) {
            $data = $event->getData();
            $this->processYoutube($data);
        });

        Oforge()->Events()->attach(Insertion::class . '::updated', Event::SYNC, function (Event $event) {
            $data = $event->getData();
            $this->processYoutube($data);
        });
    }

    private function processYoutube($data) {
        /** @var YoutubeService $youtubeService */
        $youtubeService = Oforge()->Services()->get('video.youtube');
        if (isset($data["id"])) {
            $youtubeVideo = ArrayHelper::dotGet($data, "data.youtube_video");
            $thumbnails   = ArrayHelper::dotGet($data, "data.youtube_thumbnail");

            $videoData = [];
            foreach ($youtubeVideo as $youtube) {
                if ($thumbnails != null && isset($thumbnails[$youtube]) && !empty($thumbnails[$youtube])) {
                    $videoData[] = ["videoKey" => $youtube, "thumbnail" => $thumbnails[$youtube]];
                } else {
                    $video = $youtubeService->resolveVideoData($youtube);
                    if ($video != null) {
                        $thumbnail   = ArrayHelper::dotGet($video, "thumbnail_url");
                        $videoData[] = ["videoKey" => $youtube, "thumbnail" => $thumbnail];
                    }
                }
            }

            $youtubeService->processChange($data["id"], $videoData);
        }
    }
}
