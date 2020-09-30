<?php /** @noinspection PhpComposerExtensionStubsInspection */

namespace Oforge\Engine\Modules\Media\Lib\Image;

use Imagick;
use ImagickException;
use Oforge\Engine\Modules\Core\Helper\FileHelper;
use Oforge\Engine\Modules\Core\Helper\MimeTypes;

/**
 * Class ImageHandlerImagick
 *
 * @package Oforge\Engine\Modules\Media\Lib\Image
 */
class ImageHandlerImagick extends ImageHandler {

    /** @inheritDoc */
    public function convert(string $srcFilePath, string $dstFilePath, string $dstMimeType) : bool {
        $srcMimeType = FileHelper::getMimeType($srcFilePath);
        $imageFormat = null;
        switch ($dstMimeType) {
            case MimeTypes::IMAGE_JPG:
                $imageFormat = 'jpeg';
                break;
            case MimeTypes::IMAGE_PNG:
                $imageFormat = 'png';
                break;
            default:
                // unsupported file type
                $imageFormat = null;
        }

        if ($imageFormat !== null && $srcMimeType !== $dstMimeType) {
            try {
                $imagick = new Imagick($srcFilePath);
                $imagick->setImageFormat($imageFormat);
                $imagick->writeImage($dstFilePath);
            } catch (ImagickException $exception) {
                Oforge()->Logger()->get()->error('ImagickException: ' . $exception->getMessage(), [
                    'src'         => $srcFilePath,
                    'dst'         => $dstFilePath,
                    'dstMimeType' => $dstMimeType,
                ]);
            }
        }

        return true;
    }

    /** @inheritDoc */
    public function compress(string $srcFilePath, string $dstFilePath, int $quality = 40) : bool {
        $mimeType = FileHelper::getMimeType($srcFilePath);
        try {
            $imagick = new Imagick($srcFilePath);
            $save    = true;
            switch ($mimeType) {
                case MimeTypes::IMAGE_JPG:
                    if ($quality < 0 || $quality > 100) {
                        $quality = 40;
                    }
                    $imagick->setImageFormat('jpeg');
                    $imagick->setImageCompressionQuality($quality);
                    $imagick->setSamplingFactors(['2x2', '1x1', '1x1']);
                    //$profiles = $imagick->getImageProfiles('icc', true);
                    // $imagick->stripImage();
                    // if (!empty($profiles) && isset($profiles['icc'])) {
                    //     $imagick->profileImage('icc', $profiles['icc']);
                    // }
                    $imagick->setInterlaceScheme(Imagick::INTERLACE_JPEG);
                    $imagick->setColorspace(Imagick::COLORSPACE_SRGB);
                    break;
                case MimeTypes::IMAGE_GIF:
                    $imagick->setImageFormat('gif');
                    break;
                case MimeTypes::IMAGE_PNG:
                    // $imagick->stripImage();
                    $imagick->setImageDepth(8);
                    break;
                default:
                    // unsupported file type
                    $save = false;
            }
            if ($save) {
                $imagick->writeImage($dstFilePath);
            }
        } catch (ImagickException $exception) {
            Oforge()->Logger()->get()->error('ImagickException: ' . $exception->getMessage(), [
                'src'     => $srcFilePath,
                'dst'     => $dstFilePath,
                'quality' => $quality,
            ]);
        }

        return true;
    }

    /** @inheritDoc */
    public function resize(string $srcFilePath, string $dstFilePath, $options) : bool {
        try {
            $imagick       = new Imagick($srcFilePath);
            $currentWidth  = $imagick->getImageWidth();
            $currentHeight = $imagick->getImageHeight();

            [$width, $height] = $this->resolveScaleSizes($currentWidth, $currentHeight, $options);
            if ($width !== $currentWidth && $height !== $currentHeight) {
                $imagick->scaleImage($width, (int) $height);
                $imagick->writeImage($dstFilePath);
            }
        } catch (ImagickException $exception) {
            Oforge()->Logger()->get()->error('ImagickException: ' . $exception->getMessage(), [
                'src'     => $srcFilePath,
                'dst'     => $dstFilePath,
                'options' => $options,
            ]);

            return false;
        }

        return true;
    }

}
