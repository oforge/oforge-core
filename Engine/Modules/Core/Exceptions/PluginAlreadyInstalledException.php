<?php

namespace Oforge\Engine\Modules\Core\Exceptions;

use Exception;

class PluginAlreadyInstalledException extends Exception {

    /**
     * PluginNotInstalledException constructor.
     *
     * @param $pluginName
     */
    public function __construct($pluginName) {
        parent::__construct("The plugin $pluginName is already installed. You cannot install it twice.");
    }
}
