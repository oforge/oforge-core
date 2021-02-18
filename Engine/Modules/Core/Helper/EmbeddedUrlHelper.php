<?php

namespace Oforge\Engine\Modules\Core\Helper;

/**
 * Class EmbeddedUrlTransformer
 *
 * @package Oforge\Engine\Modules\Core\Helper
 */
class EmbeddedUrlHelper
{
    private static $baseUrl;

    private function __construct()
    {
    }

    public static function resolveInArray(array &$data, string $key) : void
    {
        if ( !isset($data[$key]) || empty($data[$key])) {
            return;
        }
        $data[$key] = self::resolveInValue($data[$key]);
    }

    public static function resolveInValue(?string $text) : string
    {
        if (empty($text)) {
            return $text;
        }
        if ( !isset(self::$baseUrl)) {
            self::$baseUrl = RouteHelper::getFullUrl('/');;
        }
        $newValue = preg_replace('/((src|href)\s*=\s*[\'"])\/(var\/public\/.*)([\'"])/m', '$1' . self::$baseUrl . '$3$4', $text);

        return ($newValue ?? $text);
    }

}
