<?php

namespace Oforge\Engine\Modules\Core\Exceptions;

/**
 * Class ConfigOptionKeyNotExistException
 *
 * @package Oforge\Engine\Modules\Core\Exceptions
 */
class ConfigOptionKeyNotExistException extends \Exception {

    /**
     * ServiceNotFoundException constructor.
     *
     * @param string $name
     */
    public function __construct(string $name) {
        parent::__construct("Config key '$name' not found in options");
    }

}
