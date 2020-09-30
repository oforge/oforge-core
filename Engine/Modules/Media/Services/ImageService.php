<?php

namespace Oforge\Engine\Modules\Media\Services;

use Oforge\Engine\Modules\Core\Helper\FileHelper;
use Oforge\Engine\Modules\Media\Lib\Image\ImageHandler;
use Oforge\Engine\Modules\Media\Lib\Image\ImageHandlerGD;
use Oforge\Engine\Modules\Media\Lib\Image\ImageHandlerImagick;
use Oforge\Engine\Modules\Media\Models\Media;

class ImageService {
    /** @var ImageHandler $imageHandler */
    protected $imageHandler;

    public function __construct() {
        if (extension_loaded('imagick')) {
            $this->imageHandler = new ImageHandlerImagick();
        } elseif (extension_loaded('gd')) {
            $this->imageHandler = new ImageHandlerGD();
        } else {
            $this->imageHandler = new ImageHandler();
        }
    }

    /**
     * @param int|string|null $pathOrID
     * @param array|int $options
     */
    public function getUrl($pathOrID, $options = []) : ?string {
        if ($pathOrID === null) {
            return null;
        }
        /** @var MediaService $mediaService */
        $mediaService = Oforge()->Services()->get('media');

        if (is_int($pathOrID)) {
            $media = $mediaService->getById($pathOrID);
        } else {
            $media = $mediaService->getByPath($pathOrID);

            if ($media === null) {
                $media = $mediaService->getById($pathOrID);
            }
        }
        if ($media === null) {
            return $pathOrID;
        }

        return $this->getUrlByMedia($media, $options);
    }

    /**
     * @param Media $media
     * @param array|int $options
     *
     * @return string
     */
    public function getUrlByMedia(Media $media, $options = []) {
        $width  = 0;
        $height = 0;
        if (is_int($options)) {
            $width = $options;
        } elseif (isset($options['width'])) {
            $width = $options['width'];
        }
        if (isset($options['height'])) {
            $height = $options['height'];
        }
        if ($width > 0) {
            $absoluteSrcFilePath = ROOT_PATH . $media->getPath();
            if (file_exists($absoluteSrcFilePath)) {
                $resizedFilePathRel = FileHelper::trimExtension($media->getPath())#
                                      . '-w' . $width #
                                      . ($height > 0 ? ('-h' . $height) : '') . '.' #
                                      . FileHelper::getExtension($media->getPath());

                $resizedFilePathAbs = ROOT_PATH . $resizedFilePathRel;
                if (!file_exists($resizedFilePathAbs)) {
                    $this->resize(ROOT_PATH . $media->getPath(), $resizedFilePathAbs, $options);
                    $this->compress($resizedFilePathAbs, $resizedFilePathAbs, 40);
                }
                if (file_exists($resizedFilePathAbs)) {
                    return $resizedFilePathRel;
                }
            }
        }

        return $media->getPath();
    }

    /**
     * @param $srcFilePath
     * @param string|null $dstFilePath
     * @param array $options Key 'scale': Value Width (int) or array with keys 'width' and/or 'height' with int values.<br>
     * Key 'convert': String value with image mime types, see: Oforge\Engine\Modules\Core\Helper\MimeTypes.
     * Key 'compress': Int value with $quality (between 0 and 100).
     *
     * @return bool
     */
    public function modify($srcFilePath, ?string $dstFilePath, array $options) : bool {
        if (!file_exists($srcFilePath)) {
            return false;
        }
        $success = true;
        if (isset($options['convert'])) {
            $dstMimeType = $options['convert'];
            $success     &= $this->convert($srcFilePath, $dstFilePath, $dstMimeType);
        }
        if (isset($options['compress'])) {
            $quality = $options['compress'];
            $success &= $this->compress($srcFilePath, $dstFilePath, $quality);
        }
        if (isset($options['scale'])) {
            $scaleOptions = $options['scale'];
            $success      &= $this->resize($srcFilePath, $dstFilePath, $scaleOptions);
        }

        return $success;
    }

    /**
     * @param string $srcFilePath
     * @param string|null $dstFilePath
     * @param string $dstMimeType Value of image mime types in Oforge\Engine\Modules\Core\Helper\MimeTypes.
     *
     * @return bool
     */
    public function convert(string $srcFilePath, ?string $dstFilePath, string $dstMimeType) : bool {
        if (!file_exists($srcFilePath)) {
            return false;
        }

        return $this->imageHandler->convert($srcFilePath, FileHelper::replaceExtension($dstFilePath, $dstMimeType), $dstMimeType);
    }

    /**
     * @param string $srcFilePath
     * @param string|null $dstFilePath
     * @param int $quality
     *
     * @return bool
     */
    public function compress(string $srcFilePath, ?string $dstFilePath, int $quality = 40) : bool {
        if (!file_exists($srcFilePath)) {
            return false;
        }

        return $this->imageHandler->compress($srcFilePath, $dstFilePath, $quality);
    }

    /**
     * @param string $srcFilePath
     * @param string|null $dstFilePath
     * @param array<string,int>|int $options Width (int) or array with keys 'width' and/or 'height' with int values.
     *
     * @return bool
     */
    public function resize(string $srcFilePath, ?string $dstFilePath, $options) : bool {
        if (!file_exists($srcFilePath)) {
            return false;
        }

        return $this->imageHandler->resize($srcFilePath, $this->resolveDstFilePath($srcFilePath, $dstFilePath), $options);
    }

    /**
     * @param string $srcFilePath
     * @param string|null $dstFilePath
     *
     * @return string
     */
    protected final function resolveDstFilePath(string $srcFilePath, ?string $dstFilePath) {
        return $dstFilePath ?? $srcFilePath;
    }

}
