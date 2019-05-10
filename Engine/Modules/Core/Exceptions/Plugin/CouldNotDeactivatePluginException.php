<?php

namespace Oforge\Engine\Modules\Core\Exceptions\Plugin;

use Exception;

/**
 * Class CouldNotDeactivatePluginException
 *
 * @package Oforge\Engine\Modules\Core\Exceptions
 */
class CouldNotDeactivatePluginException extends Exception {

    /**
     * CouldNotDeactivatePluginException constructor.
     *
     * @param string $className
     * @param string[] $dependents
     */
    public function __construct(string $className, $dependents) {
        parent::__construct("The plugin '$className' could not be deactivated because there are active plugins that depend on it. Dependents: " . implode(', ',
                $dependents));
    }

}
