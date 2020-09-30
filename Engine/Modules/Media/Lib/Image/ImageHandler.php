<?php

namespace Oforge\Engine\Modules\Media\Lib\Image;

use Oforge\Engine\Modules\Core\Helper\FileHelper;
use Oforge\Engine\Modules\Core\Helper\MimeTypes;

class ImageHandler {

    /**
     * @param string $srcFilePath
     * @param string $dstFilePath
     * @param string $dstMimeType
     *
     * @return bool
     */
    public function convert(string $srcFilePath, string $dstFilePath, string $dstMimeType) : bool {
        return true;
    }

    /**
     * @param string $srcFilePath
     * @param string $dstFilePath
     * @param int $quality
     *
     * @return bool
     */
    public function compress(string $srcFilePath, string $dstFilePath, int $quality = 40) : bool {
        return true;
    }

    /**
     * @param string $srcFilePath
     * @param string $dstFilePath
     * @param array<string,int>|int $options Width (int) or array with keys 'width' and/or 'height' with int values.
     *
     * @return bool
     */
    public function resize(string $srcFilePath, string $dstFilePath, $options) : bool {
        return true;
    }

    /**
     * @param int $currentWidth
     * @param int $currentHeight
     * @param array<string,int>|int $options Width (int) or array with keys 'width' and/or 'height' with int values.
     *
     * @return int[] [width, height]
     */
    protected function resolveScaleSizes(int $currentWidth, int $currentHeight, $options) {
        $width  = $currentWidth;
        $height = $currentHeight;
        if (is_int($options)) {
            $width  = $options;
            $height = (int) (1.0 * $width / $currentWidth * $currentHeight);
        } else {
            if (isset($options['width']) && is_int($options['width'])) {
                $width = $options['width'];
            }
            if (isset($options['height']) && is_int($options['height'])) {
                $width = $options['height'];
            }
        }

        return [$width, $height];
    }

}
