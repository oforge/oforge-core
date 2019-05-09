<?php

namespace Oforge\Engine\Modules\Core\Exceptions;

use Exception;

/**
 * Class ServiceAlreadyDefinedException
 *
 * @package Oforge\Engine\Modules\Core\Exceptions
 */
class ServiceAlreadyExistException extends Exception {

    /**
     * ServiceNotFoundException constructor.
     *
     * @param string $serviceName
     */
    public function __construct(string $serviceName) {
        parent::__construct("A service with name '$serviceName' is already exist!");
    }

}
