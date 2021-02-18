<?php

namespace Oforge\Engine\Modules\Media\Helper;

use Exception;
use Oforge\Engine\Modules\Core\Exceptions\ServiceNotFoundException;
use Oforge\Engine\Modules\Core\Helper\RouteHelper;
use Oforge\Engine\Modules\Media\Services\ImageCompressService;

/**
 * Class ImageHelper
 *
 * @package CMS\Helper
 */
class ImageHelper
{
    public const DEFAULT_ADD_ORIGINAL_IMAGE_URL = false;
    private const DEBUG_SIZE = false;
    /** @var ImageCompressService $imageCompressService */
    private static $imageCompressService;

    private function __construct()
    {
    }

    public static function compressInArray(array &$data, string $key, $size = 0, bool $addOriginalImageUrl = ImageHelper::DEFAULT_ADD_ORIGINAL_IMAGE_URL) : void
    {
        if ( !isset($data[$key]) || empty($data[$key])) {
            return;
        }
        $result = self::compressSingle($data[$key], $size, $addOriginalImageUrl);
        if ($result !== null) {
            $data[$key] = $result;
        }
    }

    /**
     * @param int|string $idOrPath
     * @param int $size
     * @param bool $addOriginalImageUrl
     *
     * @return array|string|null Returns null on error, full url string for int $size, or responsive array.
     */
    public static function compressSingle($idOrPath, $size = 0, bool $addOriginalImageUrl = ImageHelper::DEFAULT_ADD_ORIGINAL_IMAGE_URL)
    {
        if (self::$imageCompressService === null) {
            try {
                self::$imageCompressService = Oforge()->Services()->get('image.compress');
            } catch (ServiceNotFoundException $exception) {
                Oforge()->Logger()->logException($exception);

                return null;
            }
        }
        $result = [];
        if ($addOriginalImageUrl) {
            try {
                $relativePath = self::$imageCompressService->getPath($idOrPath, 0);
                if ($relativePath !== null) {
                    $result['full'] = self::finaliseUrl($relativePath, 0);
                }
            } catch (Exception $exception) {
                Oforge()->Logger()->logException($exception);
            }
        }
        if (is_array($size)) {
            foreach ($size as $breakpoint => $maxSize) {
                try {
                    $relativePath = self::$imageCompressService->getPath($idOrPath, $maxSize);
                    if ($relativePath !== null) {
                        $result[$breakpoint] = self::finaliseUrl($relativePath, $maxSize);
                    }
                } catch (Exception $exception) {
                    Oforge()->Logger()->logException($exception);
                }
            }
        } else {
            try {
                $relativePath = self::$imageCompressService->getPath($idOrPath, $size);
                if ($relativePath === null) {
                    return null;
                }
                $fullUrl = self::finaliseUrl($relativePath, $size);
                if ($addOriginalImageUrl) {
                    $result[$size] = $fullUrl;
                } else {
                    return $fullUrl;
                }
            } catch (Exception $exception) {
                Oforge()->Logger()->logException($exception);

                return null;
            }
        }

        return $result;
    }

    /**
     * @param string $relativePath
     * @param int $maxSize
     *
     * @return string
     */
    private static function finaliseUrl(string $relativePath, int $maxSize) : string
    {
        if (self::DEBUG_SIZE) {
            $relativePath .= (strpos($relativePath, '?') === false ? '?' : '&') . 'size=' . $maxSize;
        }

        return RouteHelper::getFullUrl(str_replace(' ', '%20', $relativePath));
    }

}
