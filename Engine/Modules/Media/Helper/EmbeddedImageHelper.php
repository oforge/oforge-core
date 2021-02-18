<?php

namespace Oforge\Engine\Modules\Media\Helper;

/**
 * Class EmbeddedMediaHelper
 *
 * @package Oforge\Engine\Modules\Media\Helper
 */
class EmbeddedImageHelper
{
    private function __construct()
    {
    }

    public static function resolveInArray(array &$data, string $key, $size = 0)
    {
        if ( !isset($data[$key]) || empty($data[$key])) {
            return;
        }
        $data[$key] = self::resolveSingle($data[$key], $size);
    }

    /**
     * @param string|null $text
     * @param int|array<string, int> $size If array, img tag will be replaced by picture tag
     *
     * @return string
     */
    public static function resolveSingle(?string $text, $size = 0) : string
    {
        if (empty($text)) {
            return $text;
        }
        // Array
        // (
        //     [0] => <img class="asd" src="/var/public/images/path" id=asd>
        //     [1] => <img class="asd" src="
        //     [2] =>  class="asd"
        //     [3] => "
        //     [4] => var/public/images/path
        //     [5] => " id=asd>
        //     [6] => "
        //     [7] =>  id=asd
        // )
        $newText = preg_replace_callback(
            '/(<img(.*?)src=([\'"]))(\/var\/public\/images\/.*?)(([\'"])(.*?)\/?>)/m',
            function (array $matches) use ($size) {
                $imagePath  = trim($matches[4]);
                $attributes = rtrim(' ' . trim($matches[2]) . ' ' . trim($matches[7]));
                $quoteStart = trim($matches[3]);
                $quoteEnd   = trim($matches[6]);

                $imagePaths = ImageHelper::compressSingle($imagePath, $size);
                if ($imagePaths === null) {
                    return $matches[0];
                }
                if (is_array($imagePaths)) {
                    $sources = '';
                    $minBreakpoint = 999999999;
                    foreach ($imagePaths as $breakpoint => $url) {
                        if (is_numeric($breakpoint)) {
                            $minBreakpoint = min($minBreakpoint, $breakpoint);
                        }
                        $sources .= '<source srcset=' . $quoteStart . $url . $quoteEnd . ' media=' . $quoteStart . '(min-width: ' . $breakpoint . 'px)' . $quoteEnd . '/>';
                    }
                    $sources .= '<img src=' . $quoteStart . $imagePaths[$minBreakpoint] . $quoteEnd . '/>';

                    return '<picture' . $attributes . '>' . $sources . '</picture>';
                } else {
                    return '<img' . $attributes . ' src=' . $quoteStart . $imagePaths . $quoteEnd . '/>';
                }
            },
            $text
        );

        return ($newText ?? $text);
    }

}
