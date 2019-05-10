<?php

namespace Oforge\Engine\Modules\Core\Helper;

/**
 * Class SessionHelper
 *
 * @package Oforge\Engine\Modules\Core\Helper
 */
class SessionHelper {

    /**
     * Prevent instance.
     */
    private function __construct() {
    }

    /**
     * @return string
     */
    public static function generateGuid() {
        return sprintf('%04X%04X-%04X-%04X-%04X-%04X%04X%04X', mt_rand(0, 65535), mt_rand(0, 65535), mt_rand(0, 65535), mt_rand(16384, 20479),
            mt_rand(32768, 49151), mt_rand(0, 65535), mt_rand(0, 65535), mt_rand(0, 65535));
    }

}
