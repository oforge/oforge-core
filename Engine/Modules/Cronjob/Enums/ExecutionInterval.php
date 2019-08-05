<?php

namespace Oforge\Engine\Modules\Cronjob\Enums;

/**
 * Class TimeIntervalHelper
 *
 * @package Oforge\Engine\Modules\Core\Helper
 */
class ExecutionInterval {
    public const MINUTELY = 60;
    public const HOURLY   = 60 * self::MINUTELY;
    public const DAILY    = 24 * self::HOURLY;
    public const WEEKLY   = 7 * self::DAILY;
    public const MONTHLY  = 30 * self::DAILY;

    /**
     * Prevent instance.
     */
    private function __construct() {
    }

    public static function days(int $days) {
        return $days * self::DAILY;
    }

}
