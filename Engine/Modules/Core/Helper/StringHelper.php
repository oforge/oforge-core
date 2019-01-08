<?php

namespace Oforge\Engine\Modules\Core\Helper;

class StringHelper
{
    /**
     * Check if a string starts with a given value
     *
     * @param string $haystack
     * @param string $needle
     *
     * @return bool
     */
    public static function startsWith(string $haystack, string $needle)
    {
        $length = strlen($needle);
        return (substr($haystack, 0, $length) === $needle);
    }
    
    /**
     * Check if a string ends with a given value
     *
     * @param string $haystack
     * @param string $needle
     *
     * @return bool
     */
    public static function endsWith(string $haystack, string $needle)
    {
        $length = strlen($needle);
        if ($length == 0) {
            return true;
        }

        return (substr($haystack, -$length) === $needle);
    }
    
    /**
     * Check if a given value is inside a String
     *
     * @param string $haystack
     * @param string $needle
     *
     * @return bool
     */
    public static function contains(string $haystack, string $needle)
    {
        return (strpos($haystack, $needle) !== false);
    }
    
    /**
     * Check if a value is found before a given string / character.
     * If found, return that value.
     * Otherwise return the haystack
     *
     * @param string $haystack The part where you search inside
     * @param string $needle The separator
     *
     * @return mixed
     */
    public static function substringBefore(string $haystack, string $needle)
    {
        if (StringHelper::contains($haystack, $needle)) {
            return explode($needle, $haystack)[0];
        }

        // TODO: Why u return haystack, if no value found?
        // TODO: Y U NO RETURN NULL °// ?
        return $haystack;
    }
}
