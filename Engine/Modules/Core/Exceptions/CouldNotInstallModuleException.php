<?php

namespace Oforge\Engine\Modules\Core\Exceptions;

use Exception;

/**
 * Class CouldNotInstallModuleException
 *
 * @package Oforge\Engine\Modules\Core\Exceptions\Plugin
 */
class CouldNotInstallModuleException extends Exception {

    /**
     * CouldNotInstallModuleException constructor.
     *
     * @param string $moduleName
     */
    public function __construct(string $moduleName, $dependencies) {
        parent::__construct("The module '$moduleName' could not be started due to missing dependencies. Missing modules: " . implode(', ', $dependencies));
    }

}
