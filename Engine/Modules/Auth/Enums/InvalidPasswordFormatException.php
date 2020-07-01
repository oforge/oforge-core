<?php

namespace Oforge\Engine\Modules\Auth\Enums;

use Exception;

/**
 * Class InvalidPasswordFormatException
 *
 * @package Oforge\Engine\Modules\Auth\Enums
 */
class InvalidPasswordFormatException extends Exception {

    /**
     * InvalidPasswordFormatException constructor.
     *
     * @param string $message
     */
    public function __construct($message = '') {
        parent::__construct($message);
    }

}
