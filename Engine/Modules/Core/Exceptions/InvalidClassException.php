<?php

namespace Oforge\Engine\Modules\Core\Exceptions;

use Exception;

/**
 * Class InvalidClassException
 *
 * @package Oforge\Engine\Modules\Core\Exceptions
 */
class InvalidClassException extends Exception {

    /**
     * InvalidClassException constructor.
     *
     * @param string $classname
     */
    public function __construct(string $classname, string $expectedClassName) {
        parent::__construct("The class '$classname' is not a child of '$expectedClassName'.");
    }

}
