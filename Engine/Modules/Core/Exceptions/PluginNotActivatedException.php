<?php

namespace Oforge\Engine\Modules\Core\Exceptions;

use Exception;

class PluginNotActivatedException extends Exception {

    /**
     * PluginNotInstalledException constructor.
     *
     * @param $pluginName
     */
    public function __construct($pluginName) {
        parent::__construct("The plugin $pluginName is not activated. You have to activate it first.");
    }
}
