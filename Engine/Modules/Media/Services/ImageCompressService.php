<?php

namespace Oforge\Engine\Modules\Media\Services;

use Doctrine\ORM\ORMException;
use Imagick;
use ImagickException;
use Oforge\Engine\Modules\Core\Exceptions\ServiceNotFoundException;
use Oforge\Engine\Modules\Media\Models\Media;

/**
 * Class ImageCompressService
 *
 * @package Oforge\Engine\Modules\Media\Services
 */
class ImageCompressService {

    /**
     * @param string|null $path
     * @param int $width
     *
     * @return string|null
     * @throws ServiceNotFoundException
     * @throws ORMException
     */
    public function getPath(?string $path, int $width = 0) : ?string {
        if (!isset($path)) {
            return null;
        }
        /** @var MediaService $mediaService */
        $mediaService = Oforge()->Services()->get('media');
        $media        = $mediaService->getByPath($path);

        if (!isset($media)) {
            $media = $mediaService->getById($path);
            if (!isset($media)) {
                return $path;
            }
        }

        if ($width > 0) {
            $fileExtension = '';
            switch ($media->getType()) {
                case 'image/jpeg':
                    $fileExtension = 'jpeg';
                    break;
                case 'image/jpg':
                    $fileExtension = 'jpg';
                    break;
                case 'image/png':
                    $fileExtension = 'png';
                    break;
            }
            if (!empty($fileExtension)) {
                $cacheUrl = substr($media->getPath(), 0, -strlen($fileExtension)) . '_' . $width . '.' . $fileExtension;
                //File is already compressed and stored
                if (file_exists(ROOT_PATH . $cacheUrl)) {
                    return $cacheUrl;
                }
                //File should be compressed
                if (extension_loaded('imagick')) {
                    if ($this->compress($media, $width, $cacheUrl)) {
                        //File is compressed and stored
                        if (file_exists(ROOT_PATH . $cacheUrl)) {
                            return $cacheUrl;
                        }
                    }
                }
            }
        }

        return $media->getPath();
    }

    /**
     * @param Media $media
     * @param int $width
     * @param string $cacheUrl
     *
     * @return bool
     */
    public function compress(Media $media, int $width, string $cacheUrl) : bool {
        try {
            if (extension_loaded('imagick')) {
                $imagick = new Imagick(ROOT_PATH . $media->getPath());

                $widthCurrent  = $imagick->getImageWidth();
                $heightCurrent = $imagick->getImageHeight();

                $image_types = getimagesize(ROOT_PATH . $media->getPath());

                $imagick->scaleImage($width, (int) (1.0 * $width / $widthCurrent * $heightCurrent));
                // Compress image

                // Set image as based its own type
                if ($image_types[2] === IMAGETYPE_JPEG)
                {
                    $imagick->setImageFormat('jpeg');
                    $imagick->setImageCompressionQuality(40);
                    $imagick->setSamplingFactors(array('2x2', '1x1', '1x1'));

                    $profiles = $imagick->getImageProfiles("icc", true);

                    $imagick->stripImage();

                    if(!empty($profiles)) {
                        $imagick->profileImage('icc', $profiles['icc']);
                    }

                    $imagick->setInterlaceScheme(Imagick::INTERLACE_JPEG);
                    $imagick->setColorspace(Imagick::COLORSPACE_SRGB);
                }
                else if ($image_types[2] === IMAGETYPE_PNG)
                {

                    $imagick->setImageCompressionQuality(60);
                    $imagick->setImageFormat('png');
                }
                else if ($image_types[2] === IMAGETYPE_GIF)
                {
                    $imagick->setImageFormat('gif');
                }

                $imagick->writeImage(ROOT_PATH . $cacheUrl);

                return true;
            }
        } catch (ImagickException $e) {
            Oforge()->Logger()->get()->error('ImagickException', $e->getTrace());
        }

        return false;
    }

}
