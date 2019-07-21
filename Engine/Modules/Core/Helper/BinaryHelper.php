<?php

namespace Oforge\Engine\Modules\Core\Helper;

/**
 * BinaryHelper
 *
 * @package Oforge\Engine\Modules\Core\Helper
 */
class BinaryHelper {

    /**
     * Prevent instance.
     */
    private function __construct() {
    }

    /**
     * Add flag.
     *
     * @param int $flags
     * @param int $flag
     *
     * @return bool
     */
    public static function add(int $flags, int $flag) : bool {
        return $flags | $flag;
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
