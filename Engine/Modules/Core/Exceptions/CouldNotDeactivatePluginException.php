<?php

namespace Oforge\Engine\Modules\Core\Exceptions;

class CouldNotDeactivatePluginException extends \Exception
{
    /**
     * CouldNotDeactivatePluginException constructor.
     * @param string $classname
     * @param array $depentends
     */
    public function __construct($classname, $dependents)
    {
        parent::__construct("The plugin $classname could not be deactivated because there are active plugins that depend on it. Dependents: " . implode(", ", $dependents));
    }
}
