<?php

namespace Oforge\Engine\Modules\Media\EventHandler;

use Oforge\Engine\Modules\Core\Manager\Events\Event;
use Oforge\Engine\Modules\Core\Services\ConfigService;
use Oforge\Engine\Modules\Media\Models\Media;
use Oforge\Engine\Modules\Media\Services\ImageCompressService;

class MediaImageEvent
{

    public static function register()
    {
        Oforge()->Events()->attach('media.image::created', Event::SYNC, [self::class, 'handleImageCreated']);
    }

    public function handleImageCreated(Event $event)
    {
        /** @var Media $media */
        $media = $event->getData()['media'];
        /** @var ConfigService $configService */
        $configService = Oforge()->Services()->get('config');
        if ($configService->get('media_upload_image_adjustment_enabled')) {
            /** @var ImageCompressService $imageCompressService */
            $imageCompressService = Oforge()->Services()->get('image.compress');
            if (($downscalingMaxWidth = $configService->get('media_upload_image_adjustment_downscaling_max_width')) > 0) {
                $imageCompressService->scale($media, $downscalingMaxWidth, $media->getPath());
            }
            if ($configService->get('media_upload_image_adjustment_compress')) {
                $imageCompressService->compress($media->getPath());
            }
        }
    }

}
