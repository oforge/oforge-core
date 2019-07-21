<?php

namespace Oforge\Engine\Modules\Core\Exceptions;

use Exception;

/**
 * Class LoggerAlreadyExistException
 *
 * @package Oforge\Engine\Modules\Core\Exceptions
 */
class LoggerAlreadyExistException extends Exception {

    /**
     * LoggerAlreadyExistException constructor.
     *
     * @param string $loggerName
     */
    public function __construct(string $loggerName) {
        parent::__construct("Logger with name '$loggerName' already exist!");
    }

}
