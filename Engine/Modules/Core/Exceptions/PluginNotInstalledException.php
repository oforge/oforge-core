<?php

namespace Oforge\Engine\Modules\Core\Exceptions;

use Exception;

class PluginNotInstalledException extends Exception {

    /**
     * PluginNotInstalledException constructor.
     *
     * @param $pluginName
     */
    public function __construct($pluginName) {
        parent::__construct("The plugin $pluginName is not installed. You have to install it first.");
    }
}
