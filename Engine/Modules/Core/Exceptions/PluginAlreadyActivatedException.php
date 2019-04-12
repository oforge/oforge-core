<?php

namespace Oforge\Engine\Modules\Core\Exceptions;

use Exception;

class PluginAlreadyActivatedException extends Exception {

    /**
     * PluginNotInstalledException constructor.
     *
     * @param $pluginName
     */
    public function __construct($pluginName) {
        parent::__construct("The plugin $pluginName is already activated. You cannot activate it twice.");
    }
}
