<?php

namespace Oforge\Engine\Modules\Core\Exceptions;

class CouldNotActivatePluginException extends \Exception
{
    /**
     * CouldNotActivatePluginException constructor.
     * @param string $classname
     */
    public function __construct($classname, $dependencies)
    {
        parent::__construct("The plugin $classname could not be activated due to missing / not installed / not activated dependencies. Missing plugins: " . implode(", ", $dependencies));
    }
}
