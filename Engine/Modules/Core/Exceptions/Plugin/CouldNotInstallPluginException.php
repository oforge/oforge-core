<?php

namespace Oforge\Engine\Modules\Core\Exceptions\Plugin;

use Exception;

/**
 * Class CouldNotInstallPluginException
 *
 * @package Oforge\Engine\Modules\Core\Exceptions
 */
class CouldNotInstallPluginException extends Exception {

    /**
     * CouldNotInstallPluginException constructor.
     *
     * @param string $className
     * @param string[] $dependencies
     */
    public function __construct(string $className, $dependencies) {
        parent::__construct("The plugin '$className' could not be started due to missing dependencies. Missing plugins: " . implode(', ', $dependencies));
    }

}
