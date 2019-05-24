<?php

namespace Oforge\Engine\Modules\Media\Services;

use Oforge\Engine\Modules\Media\Models\Media;

class ImageCompressService {

    public function getPath(?string $path, int $width) : ?string {
        if(!isset($path)) return null;
        /** @var MediaService $configService */
        $configService = Oforge()->Services()->get('media');
        $media         = $configService->getByPath($path);

        if (!isset($media)) {
            $media = $configService->get($path);
            if (!isset($media)) {
                return $path;
            }
        }

        $suffix = "";

        switch ($media->getType()) {
            case "image/jpeg":
            case "image/jpg":
                $suffix = "jpg";
                break;
            case "image/png":
                $suffix = "png";
                break;
        }

        if (!empty($suffix)) {
            $cacheUrl = substr($media->getPath(), 0, -4) . "_" . $width . "." . $suffix;

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

        //Fallback: return default path
        return $media->getPath();
    }

    public function compress(Media $media, int $width, string $cacheUrl) : bool {
        try {
            if (extension_loaded('imagick')) {
                $image = new \Imagick(ROOT_PATH . $media->getPath());

                $widthCurrent  = $image->getImageWidth();
                $heightCurrent = $image->getImageHeight();

                $image->scaleImage($width, (int) (1.0 * $width / $widthCurrent * $heightCurrent));

                $image->writeImage(ROOT_PATH . $cacheUrl);

                return true;
            }
        } catch (\ImagickException $e) {
            Oforge()->Logger()->get()->error("ImagickException", $e);
        }

        return false;
    }
}
