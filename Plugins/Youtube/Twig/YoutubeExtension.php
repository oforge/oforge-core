<?php

namespace Youtube\Twig;

use DateTime;
use DateTimeInterface;
use Doctrine\ORM\ORMException;
use Insertion\Services\AttributeService;
use Oforge\Engine\Modules\Core\Exceptions\ServiceNotFoundException;
use Twig_Extension;
use Twig_ExtensionInterface;
use Twig_Filter;
use Twig_Function;
use Youtube\Services\YoutubeService;

/**
 * Class AccessExtension
 *
 * @package Oforge\Engine\Modules\TemplateEngine\Extensions\Twig
 */
class YoutubeExtension extends Twig_Extension implements Twig_ExtensionInterface {

    /**
     * @inheritDoc
     */
    public function getFunctions() {
        return [
            new Twig_Function('getYoutubeData', [$this, 'getYoutubeData']),
        ];
    }

    /**
     * @param mixed ...$vars
     *
     * @return mixed|string
     */
    public function getYoutubeData(...$vars) {
        $result = '';
        if (count($vars) == 1) {
            if (isset($vars[0]["id"])) {
                /** @var YoutubeService $youtubeService */
                $youtubeService = Oforge()->Services()->get('video.youtube');

                $video = $youtubeService->getYoutubeVideo($vars[0]["id"]);
                if ($video != null) {
                    return $video->getVideoKey();
                }
            }

            if (isset($vars[0]["youtube_video"])) {
                return $vars[0]["youtube_video"];
            }


        }
    }
}
