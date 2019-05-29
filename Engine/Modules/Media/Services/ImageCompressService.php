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
                    $suffix = 'jpeg';
                    break;
                case 'image/jpg':
                    $suffix = 'jpg';
                    break;
                case 'image/png':
                    $suffix = 'png';
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
                $image = new Imagick(ROOT_PATH . $media->getPath());

                $widthCurrent  = $image->getImageWidth();
                $heightCurrent = $image->getImageHeight();

                $image->scaleImage($width, (int) (1.0 * $width / $widthCurrent * $heightCurrent));
                $image->writeImage(ROOT_PATH . $cacheUrl);

                return true;
            }
        } catch (ImagickException $e) {
            Oforge()->Logger()->get()->error('ImagickException', $e->getTrace());
        }

        return false;
    }

}
