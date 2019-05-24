<?php

namespace Oforge\Engine\Modules\Core\Helper;

/**
 * PhpArrayFileWriter
 * Writes a php array return file.
 *
 * @package Oforge\Engine\Modules\Core\Helper
 */
class ArrayPhpFileStorage {

    /**
     * Prevent instance.
     */
    private function __construct() {
    }

    /**
     * Load stored array from file.
     *
     * @param string $filepath
     *
     * @return array
     */
    public static function load(string $filepath) : array {
        if (file_exists($filepath)) {
            return include($filepath);
        }

        return [];
    }

    /**
     * Store array as php file.
     *
     * @param string $filepath
     * @param array $array
     *
     * @return bool True on success.
     */
    public static function write(string $filepath, array $array) : bool {
        $text = '<?php return [';
        self::convert($text, $array, 1);
        $text .= PHP_EOL . '];';

        return false !== file_put_contents($filepath, $text, LOCK_EX);
    }

    /**
     * Convert array to valid php array file string representation.
     *
     * @param array $array
     *
     * @return string
     */
    public static function arrayToString(array $array) {
        $text = '[';
        self::convert($text, $array, 1);
        $text .= PHP_EOL . '];';

        return $text;
    }

    /**
     * @param string $text
     * @param array $array
     * @param int $indent
     */
    private static function convert(string &$text, array $array, int $indent) {
        $isAssoc = ArrayHelper::isAssoc($array);
        foreach ($array as $key => $value) {
            $text .= PHP_EOL . str_repeat(' ', 4 * $indent);
            if ($isAssoc) {
                if (is_int($key)) {
                    $text .= "$key => ";
                } else {
                    $text .= "'$key' => ";
                }
            }
            if (is_array($value)) {
                $text .= '[';
                self::convert($text, $value, $indent + 1);
                $text .= PHP_EOL . str_repeat(' ', 4 * $indent) . ']';
            } else {
                $text .= var_export($value, true);
            }
            $text .= ',';
            // $text .= PHP_EOL . str_repeat(' ', 4 * $indent);
        }
    }

}
