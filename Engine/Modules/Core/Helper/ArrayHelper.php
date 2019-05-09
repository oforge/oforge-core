<?php

namespace Oforge\Engine\Modules\Core\Helper;

/**
 * ArrayHelper
 *
 * @package Oforge\Engine\Modules\Core\Helper
 */
class ArrayHelper {

    /**
     * Prevent instance.
     */
    private function __construct() {
    }

    /**
     * Check when array is associative.
     *
     * @param array $array
     *
     * @return bool
     */
    public static function isAssoc(array $array) : bool {
        return ($array !== array_values($array));
    }

    /**
     * Returns array value or default.
     * Check array key with isset.
     *
     * @param array $array
     * @param mixed $key
     * @param mixed $defaultValue
     *
     * @return mixed
     */
    public static function get(array $array, $key, $defaultValue = null) {
        return isset($array[$key]) ? $array[$key] : $defaultValue;
    }

    /**
     * Returns array value or default.
     * Check array key with array_key_exists.
     *
     * @param array $array
     * @param mixed $key
     * @param mixed $defaultValue
     *
     * @return mixed
     */
    public static function getNullable(array $array, $key, $defaultValue = null) {
        return array_key_exists($key, $array) ? $array[$key] : $defaultValue;
    }

    /**
     * Create array with given keys and default value, then merge values of $inputArray
     *
     * @param array $keys
     * @param array $inputArray
     * @param string $defaultValue
     *
     * @return array
     */
    public static function extractArray(array $keys, array $inputArray, $defaultValue = '') : array {
        $tmp = array_fill_keys($keys, $defaultValue);

        return array_replace($tmp, array_intersect_key($inputArray, $tmp));
    }

    /**
     * @param array $array1
     * @param array $array2
     *
     * @return array
     */
    public static function mergeRecursive(array $array1, array $array2) {
        foreach ($array2 as $key => &$value) {
            if (is_array($value) && isset($array1[$key]) && is_array($array1[$key])) {
                if (is_string($key)) {
                    $array1[$key] = is_array($array1[$key]) ? self::mergeRecursive($array1[$key], $value) : $value;
                } else {
                    $array1[] = $value;
                }
            } elseif (is_numeric($key)) {
                if (!in_array($value, $array1)) {
                    $array1[] = $value;
                }
            } else {
                $array1[$key] = $value;
            }
        }
        unset($value);

        return $array1;
    }

    /**
     * Get value by key in dot notation or $default if not exist.
     *
     * @param array $array
     * @param string $key
     * @param mixed $default
     *
     * @return mixed
     */
    public static function dotGet(array $array, string $key, $default = null) {
        if (empty($key) || empty($array)) {
            return $default;
        }
        if (strpos($key, '.') !== false) {
            $keys = explode('.', $key);
            $tmp  = $array;
            foreach ($keys as $key) {
                if (!isset($tmp[$key])) {
                    return $default;
                }

                $tmp = $tmp[$key];
            }

            return $tmp;
        }

        return isset($array[$key]) ? $array[$key] : $default;
    }

    /**
     * Set value in array by key in dot notation (e.g. meta.rout.name).
     *
     * @param array $array
     * @param string $key
     * @param mixed $value
     *
     * @return array
     */
    public static function dotSet(array $array, string $key, $value) {
        if (strpos($key, '.') !== false) {
            $tmp  = &$array;
            $keys = explode('.', $key);
            foreach ($keys as $key) {
                if (!isset($tmp[$key])) {
                    $tmp[$key] = [];
                } elseif (!is_array($tmp[$key])) {
                    $tmp[$key] = [$tmp[$key]];
                }
                $tmp = &$tmp[$key];
            }
            $tmp = $value;
        }

        return $array;
    }

    /**
     * Convert keys in dot notation to nested array.
     *
     * @param array $array
     *
     * @return array
     */
    public static function dotToNested(array $array) : array {
        $result = [];
        foreach ($array as $key => $value) {
            if (is_array($value)) {
                $value = self::dotToNested($value);
            }
            $tmp    = (strpos($key, '.') === false ? [$key => $value] : self::dotSet([], $key, $value));
            $result = self::mergeRecursive($result, $tmp);
        }

        return $result;
    }

}
