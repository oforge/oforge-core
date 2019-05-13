<?php

namespace Oforge\Engine\Modules\Core\Exceptions\Plugin;

use Exception;

/**
 * Class CouldNotActivatePluginException
 *
 * @package Oforge\Engine\Modules\Core\Exceptions
 */
class CouldNotActivatePluginException extends Exception {

    /**
     * CouldNotActivatePluginException constructor.
     *
     * @param string $className
     * @param string[] $dependencies
     */
    public function __construct(string $className, $dependencies) {
        parent::__construct("The plugin $className could not be activated due to missing / not installed / not activated dependencies. Missing plugins: "
                            . implode(', ', $dependencies));
    }

}
