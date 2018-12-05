<?php

namespace Oforge\Engine\Modules\Core\Exceptions;

class InvalidClassException extends \Exception
{
    /**
     * InvalidClassException constructor.
     * @param string $classname
     */
    public function __construct($classname, $expectedClassName)
    {
        parent::__construct("The class $classname is not a child of $expectedClassName." );
    }
}