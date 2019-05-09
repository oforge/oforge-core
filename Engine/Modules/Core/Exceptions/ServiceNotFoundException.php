<?php

namespace Oforge\Engine\Modules\Core\Exceptions;

/**
 * Class ServiceNotFoundException
 *
 * @package Oforge\Engine\Modules\Core\Exceptions
 */
class ServiceNotFoundException extends NamedObjectNotFoundException {

    /**
     * ServiceNotFoundException constructor.
     *
     * @param string $serviceName
     */
    public function __construct(string $serviceName)
    {
        parent::__construct("Service with name '$serviceName' not found!");
    }
}
