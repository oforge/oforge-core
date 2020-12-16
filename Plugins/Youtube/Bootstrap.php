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
            /** @var YoutubeService $youtubeService */
            $youtubeService = Oforge()->Services()->get('video.youtube');
            if (isset($data["id"])) {
                $youtubeVideo = ArrayHelper::dotGet($data, "data.youtube_video");
                $youtubeService->processChange($data["id"], $youtubeVideo);
            }
        });

        Oforge()->Events()->attach(Insertion::class . '::updated', Event::SYNC, function (Event $event) {
            $data = $event->getData();
            /** @var YoutubeService $youtubeService */
            $youtubeService = Oforge()->Services()->get('video.youtube');
            if (isset($data["id"])) {
                $youtubeVideo = ArrayHelper::dotGet($data, "data.youtube_video");

                $youtubeService->processChange($data["id"], $youtubeVideo);
            }
        });
    }
}
