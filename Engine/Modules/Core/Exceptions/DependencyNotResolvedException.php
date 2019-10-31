<?php

namespace Oforge\Engine\Modules\Core\Exceptions;

use Exception;

/**
 * Class DependyNotResolvedException
 *
 * @package Oforge\Engine\Modules\Core\Exceptions
 */
class DependencyNotResolvedException extends Exception {
    public function __construct(string $pluginName) {
        parent::__construct('Dependency for ' . $pluginName . ' could not be resolved.');
    }
}
