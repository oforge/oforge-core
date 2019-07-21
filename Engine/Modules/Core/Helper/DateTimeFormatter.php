<?php

namespace Oforge\Engine\Modules\Core\Helper;

use DateTimeInterface;
use Exception;
use Oforge\Engine\Modules\Core\Services\ConfigService;

/**
 * Class DateTimeFormatter
 *
 * @package Oforge\Engine\Modules\Core\Helper
 */
class DateTimeFormatter {
    /** @var ConfigService $configService */
    private static $configService;

    /** Prevent instance. */
    public function __construct() {
    }

    /**
     * @param DateTimeInterface|null $dateTimeObject
     *
     * @return string
     */
    public static function date(?DateTimeInterface $dateTimeObject) : string {
        return self::format($dateTimeObject, 'system_format_date', 'd.m.Y');
    }

    /**
     * @param DateTimeInterface|null $dateTimeObject
     *
     * @return string
     */
    public static function datetime(?DateTimeInterface $dateTimeObject) : string {
        return self::format($dateTimeObject, 'system_format_datetime', 'd.m.Y H:i:s');
    }

    /**
     * @param DateTimeInterface|null $dateTimeObject
     *
     * @return string
     */
    public static function time(?DateTimeInterface $dateTimeObject) : string {
        return self::format($dateTimeObject, 'system_format_time', 'H:i:s');
    }

    /**
     * Format DateTimeObjects.
     *
     * @param DateTimeInterface|null $dateTimeObject
     * @param string $configKey
     * @param string $defaultFormat
     *
     * @return string
     */
    private static function format(?DateTimeInterface $dateTimeObject, string $configKey, string $defaultFormat) : string {
        if (!isset($dateTimeObject)) {
            return '';
        }
        try {
            if (!isset(self::$configService)) {
                self::$configService = Oforge()->Services()->get('config');
            }
            $format = self::$configService->get($configKey);

        } catch (Exception $exception) {
            $format = $defaultFormat;
        }

        return $dateTimeObject->format($format);
    }

}
