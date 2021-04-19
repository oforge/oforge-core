<?php

namespace Oforge\Engine\Modules\Media\Services;

use Imagick;
use ImagickException;
use Oforge\Engine\Modules\Media\Models\Media;

/**
 * Class ImageCompressService
 *
 * @package Oforge\Engine\Modules\Media\Services
 */
class ImageRotateService
{

    public function rotate(Media $media, int $rotate)
    {
        if (extension_loaded('imagick')) {
            try {
                $imagick = new Imagick(ROOT_PATH . $media->getPath());
                $imagick->rotateImage('#00000000', $rotate);
            } catch (ImagickException $e) {
                Oforge()->Logger()->get()->error('ImagickException', $e->getTrace());
            }
        }
    }

}
