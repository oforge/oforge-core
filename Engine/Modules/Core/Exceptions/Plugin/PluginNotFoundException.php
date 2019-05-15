<?php

namespace Oforge\Engine\Modules\Core\Exceptions\Plugin;

use Exception;

/**
 * Class PluginNotFoundException
 *
 * @package Oforge\Engine\Modules\Core\Exceptions
 */
class PluginNotFoundException extends Exception {

    /**
     * CouldNotInstallPluginException constructor.
     *
     * @param string $classname
     */
    public function __construct($classname) {
        parent::__construct("Plugin with name '$classname' not found!");
    }

}
