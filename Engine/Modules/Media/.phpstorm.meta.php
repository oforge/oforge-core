<?php

namespace PHPSTORM_META {

    if (function_exists('override')) {
        /**
         * Services
         */
        override(
            \Oforge\Engine\Modules\Core\Manager\Services\ServiceManager::get(0),
            map([
                    'media'          => \Oforge\Engine\Modules\Media\Services\MediaService::class,
                    'media.compress' => \Oforge\Engine\Modules\Media\Services\ImageCompressService::class,
                ])
        );
        /**
         * Events
         */
        override(
            \Oforge\Engine\Modules\Core\Manager\Events\Event::create(0),
            map(
                [
                    'media.binary::created' => '',
                    'media.image::created'  => '',
                    'media.video::created'  => '',
                ]
            )
        );
        override(
            \Oforge\Engine\Modules\Core\Manager\Events\EventManager::attach(0),
            map([
                    'media.binary::created' => '',
                    'media.image::created'  => '',
                    'media.video::created'  => '',
                ])
        );
    }
}
