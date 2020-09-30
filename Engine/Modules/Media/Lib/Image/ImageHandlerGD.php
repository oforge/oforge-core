<?php /** @noinspection PhpComposerExtensionStubsInspection */

namespace Oforge\Engine\Modules\Media\Lib\Image;

use Oforge\Engine\Modules\Core\Helper\FileHelper;
use Oforge\Engine\Modules\Core\Helper\MimeTypes;

class ImageHandlerGD extends ImageHandler {

    /** @inheritDoc */
    public function convert(string $srcFilePath, string $dstFilePath, string $dstMimeType) : bool {
        $image = $this->loadImage($srcFilePath);
        if ($image === false) {
            return false;
        }
        $srcMimeType = FileHelper::getMimeType($srcFilePath);
        switch ($srcMimeType) {
            case MimeTypes::IMAGE_JPG:
                return $this->convertJPG2($image, $dstFilePath, $dstMimeType);
            case MimeTypes::IMAGE_PNG:
                return $this->convertPNG2($image, $dstFilePath, $dstMimeType);
        }

        return false;
    }

    /** @inheritDoc */
    public function compress(string $srcFilePath, string $dstFilePath, int $quality = 40) : bool {
        $image = $this->loadImage($srcFilePath);
        if ($image === false) {
            return false;
        }
        $mimeType = FileHelper::getMimeType($srcFilePath);

        return $this->saveImage($image, $dstFilePath, $mimeType, 100);
    }

    /** @inheritDoc */
    public function resize(string $srcFilePath, string $dstFilePath, $options) : bool {
        $image = $this->loadImage($srcFilePath);
        if ($image === false) {
            return false;
        }
        $mimeType      = FileHelper::getMimeType($srcFilePath);
        $currentWidth  = $this->getWidth($image);
        $currentHeight = $this->getHeight($image);

        [$width, $height] = $this->resolveScaleSizes($currentWidth, $currentHeight, $options);
        if ($width === $currentWidth && $height === $currentHeight) {
            return $this->saveImage($image, $dstFilePath, $mimeType);
        }
        $scaledImage = imagecreatetruecolor($width, $height);
        if ($scaledImage !== false) {
            imagecopyresampled($scaledImage, $image, 0, 0, 0, 0, $width, $height, $currentWidth, $currentHeight);
            imagedestroy($image);
        }
        if ($scaledImage === false) {
            return false;
        }

        return $this->saveImage($scaledImage, $dstFilePath, $mimeType);
    }

    protected function convertJPG2($image, $dstFilePath, $dstMimeType) : bool {
        switch ($dstMimeType) {
            case MimeTypes::IMAGE_PNG:
            case MimeTypes::IMAGE_WEBP:
                return $this->saveImage($image, $dstFilePath, $dstMimeType);
        }

        return false;
    }

    protected function convertPNG2($image, $dstFilePath, $dstMimeType) : bool {
        $supportedDstMimeType = false;
        switch ($dstMimeType) {
            case MimeTypes::IMAGE_JPG:
            case MimeTypes::IMAGE_WEBP:
                $supportedDstMimeType = true;
        }
        if ($supportedDstMimeType) {
            switch ($dstMimeType) {
                case MimeTypes::IMAGE_JPG:
                case MimeTypes::IMAGE_WEBP:
                    return $this->saveImage($image, $dstFilePath, $dstMimeType);
            }
        }

        return false;
    }

    /**
     * @param resource $image
     *
     * @return int
     */
    protected function getWidth($image) : int {
        return (int) imagesx($image);
    }

    /**
     * @param resource $image
     *
     * @return int
     */
    protected function getHeight($image) : int {
        return (int) imagesy($image);
    }

    /**
     * @param string $filePath
     *
     * @return false|resource
     */
    protected function loadImage(string $filePath) {
        $mimeType = FileHelper::getMimeType($filePath);
        switch ($mimeType) {
            case MimeTypes::IMAGE_GIF:
                return imagecreatefromgif($filePath);
            case MimeTypes::IMAGE_JPG:
                return imagecreatefromjpeg($filePath);
            case MimeTypes::IMAGE_PNG:
                $image         = imagecreatefrompng($filePath);
                $currentWidth  = $this->getWidth($image);
                $currentHeight = $this->getHeight($image);
                $imageCopy     = imagecreatetruecolor($currentWidth, $currentHeight);
                if ($imageCopy === false) {
                    return $image;
                }
                imagefill($imageCopy, 0, 0, imagecolorallocate($imageCopy, 255, 255, 255));
                imagealphablending($imageCopy, true);
                imagecopy($imageCopy, $image, 0, 0, 0, 0, $currentWidth, $currentHeight);
                imagedestroy($image);

                return $imageCopy;
            case MimeTypes::IMAGE_WEBP:
                return imagecreatefromwebp($filePath);
            default:
                return false;
        }
    }

    /**
     * @param resource $image
     * @param string $dstFilePath
     * @param string|null $dstMimeType
     * @param int $quality
     *
     * @return bool
     */
    protected function saveImage($image, string $dstFilePath, ?string $dstMimeType, int $quality = 100) {
        if ($dstMimeType === null) {
            $dstMimeType = FileHelper::getMimeType($dstFilePath);
        }
        if ($quality < 0 || $quality > 100) {
            $quality = 100;
        }
        $success = false;
        switch ($dstMimeType) {
            case MimeTypes::IMAGE_GIF:
                imagesavealpha($image, true);
                $success = imagegif($image, $dstFilePath);
                break;
            case MimeTypes::IMAGE_JPG:
                imageinterlace($image, true);
                $success = imagejpeg($image, $dstFilePath, $quality);
                break;
            case MimeTypes::IMAGE_PNG:
                imagesavealpha($image, true);
                $quality = (int) ($quality === 100 ? 9 : ($quality / 10));
                $success = imagepng($image, $dstFilePath, $quality);
                break;
            case MimeTypes::IMAGE_WEBP:
                if (function_exists('imagewebp')) {
                    imagesavealpha($image, true);
                    $success = imagewebp($image, $dstFilePath, $quality);
                }
                break;
            default:
                // unsupported mime type
        }
        imagedestroy($image);

        return $success;
    }

}
