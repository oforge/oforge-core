<?php

namespace Oforge\Engine\Modules\Cronjob\Abstracts;

use Monolog\Logger;

/**
 * Class AbstractCronjobHandler
 *
 * @package Oforge\Engine\Modules\Cronjob\Abstracts
 */
abstract class AbstractCronjobHandler {

    /**
     * Cronjob handle.
     *
     * @param Logger $logger
     */
    abstract public function handle(Logger $logger) : void;

}
