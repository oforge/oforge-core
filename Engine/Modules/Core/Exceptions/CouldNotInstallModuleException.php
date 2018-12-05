<?php
/**
 * Created by PhpStorm.
 * User: Matthaeus.Schmedding
 * Date: 22.11.2018
 * Time: 11:53
 */

namespace Oforge\Engine\Modules\Core\Exceptions;

class CouldNotInstallModuleException extends \Exception
{
    /**
     * CouldNotInstallModuleException constructor.
     * @param string $classname
     */
    public function __construct($classname, $dependencies)
    {
        parent::__construct("The module $classname could not be started due to missing dependencies. Missing modules: " . implode(", ", $dependencies));
    }
}
