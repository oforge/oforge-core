<?php

namespace Oforge\Engine\Modules\Core\Helper;

/**
 * BitmaskHelper
 *
 * @package Oforge\Engine\Modules\Core\Helper
 */
class BitmaskHelper {

    /**
     * Prevent instance.
     */
    private function __construct() {
    }

    /**
     * Add flag.
     *
     * @param int $currentFlags
     * @param int ...$flags
     *
     * @return bool
     */
    public static function add(int $currentFlags, int...$flags) : bool {
        foreach ($flags as $flag) {
            $currentFlags = $currentFlags | $flag;
        }

        return $currentFlags;
    }

    /**
     * Check, if flag is set.
     *
     * @param int $flags
     * @param int $flag
     *
     * @return bool
     */
    public static function is(int $flags, int $flag) : bool {
        return (bool) ($flags & $flag);
    }

}
