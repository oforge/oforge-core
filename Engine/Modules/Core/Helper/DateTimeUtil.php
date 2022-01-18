<?php

namespace Oforge\Engine\Modules\Core\Helper;

use DateInterval;
use DateTimeImmutable;

class DateTimeUtil
{

    /** Prevent instance. */
    private function __construct()
    {
    }

    /**
     * @param string $dateIntervalString
     *
     * @return int
     * @throws \Exception
     */
    public static function dateIntervalStringToSeconds(string $dateIntervalString) : int
    {
        $now     = new DateTimeImmutable();
        $changed = $now->add(new DateInterval($dateIntervalString));

        return (int)($changed->getTimestamp() - $now->getTimestamp());
    }

}
