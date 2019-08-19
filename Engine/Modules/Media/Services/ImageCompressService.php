<?php

namespace Oforge\Engine\Modules\Media\Services;

use Doctrine\ORM\ORMException;
use Imagick;
use ImagickException;
use Insertion\Models\InsertionMedia;
use Oforge\Engine\Modules\Core\Abstracts\AbstractDatabaseAccess;
use Oforge\Engine\Modules\Core\Exceptions\ServiceNotFoundException;
use Oforge\Engine\Modules\Media\Models\Media;

/**
 * Class ImageCompressService
 *
 * @package Oforge\Engine\Modules\Media\Services
 */
class ImageCompressService extends AbstractDatabaseAccess {

    public function __construct() {
        parent::__construct(['default' => Media::class, 'insertionMedia' => InsertionMedia::class]);
    }

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
            $fileExtension = $this->getFileExtension($media);

            if (!empty($fileExtension)) {
                $cacheUrl = substr($media->getPath(), 0, -strlen($fileExtension) - 1) . '_' . $width . '.' . $fileExtension;
                //File is already compressed and stored
                if (file_exists(ROOT_PATH . $cacheUrl)) {
                    return $cacheUrl;
                }
                //File should be compressed
                if (extension_loaded('imagick')) {
                    $oldMedia = $media;
                    $media    = $this->compress($media);

                    // if compression fails
                    if ($media === null) {
                        $media = $oldMedia;
                    }

                    if ($oldMedia !== $media) {
                        $this->entityManager()->update($media);
                    }

                    $this->scale($media, $width, $cacheUrl);

                    if (file_exists(ROOT_PATH . $cacheUrl)) {
                        return $cacheUrl;
                    }
                }
            }
        }

        return $media->getPath();
    }

    public function getFileExtension(Media $media) {
        $fileExtension    = '';
        $tmpFileExtension = pathinfo($media->getPath(), PATHINFO_EXTENSION);
        switch ($media->getType()) {
            case 'image/jpeg':
            case 'image/jpg':
            case 'image/png':
                $fileExtension = $tmpFileExtension;
                break;
        }

        return $fileExtension;
    }

    /**
     * @param Media $media
     *
     * @return Media|null
     */
    public function compress(Media $media) : ?Media {
        try {
            if (extension_loaded('imagick')) {
                $imagick     = new Imagick(ROOT_PATH . $media->getPath());
                $image_types = getimagesize(ROOT_PATH . $media->getPath());
                // Compress image

                // Set image as based its own type
                if ($image_types[2] === IMAGETYPE_JPEG || $image_types[2] === IMAGETYPE_PNG) {
                    $imagick->setImageFormat('jpeg');
                    $imagick->setImageCompressionQuality(40);
                    $imagick->setSamplingFactors(['2x2', '1x1', '1x1']);
                    $profiles = $imagick->getImageProfiles("icc", true);
                    $imagick->stripImage();
                    if (!empty($profiles)) {
                        $imagick->profileImage('icc', $profiles['icc']);
                    }

                    $imagick->setInterlaceScheme(Imagick::INTERLACE_JPEG);
                    $imagick->setColorspace(Imagick::COLORSPACE_SRGB);

                    if ($image_types[2] === IMAGETYPE_PNG) {
                        $media->setName(str_replace('.png', '.jpeg', $media->getName()));
                        $media->setPath(str_replace('.png', '.jpeg', $media->getPath()));
                        $media->setType('image/jpeg');
                    }

                } elseif ($image_types[2] === IMAGETYPE_GIF) {
                    $imagick->setImageFormat('gif');
                } else {
                    // not supported file type
                    return null;
                }

                $imagick->writeImage(ROOT_PATH . $media->getPath());

                return $media;
            }
        } catch (ImagickException $e) {
            Oforge()->Logger()->get()->error('ImagickException', $e->getTrace());
        }

        return null;
    }

    public function scale(Media $media, int $width, string $cacheUrl) {
        try {
            if (extension_loaded('imagick')) {
                $imagick       = new Imagick(ROOT_PATH . $media->getPath());
                $widthCurrent  = $imagick->getImageWidth();
                $heightCurrent = $imagick->getImageHeight();
                $imagick->scaleImage($width, (int) (1.0 * $width / $widthCurrent * $heightCurrent));
                $imagick->writeImage(ROOT_PATH . $cacheUrl);
            }
        } catch (ImagickException $e) {
            Oforge()->Logger()->get()->error('ImagickException', $e->getTrace());
        }
    }

    /**
     * go through all medias that are stored in the database and compress them.
     *
     * @throws ORMException
     */
    public function convertAllImages() {
        $allMedia = $this->repository()->findAll();

        foreach ($allMedia as $media) {
            $media = $this->compress($media);
            if ($media) {
                $this->entityManager()->update($media);
            }
        }
    }
}
