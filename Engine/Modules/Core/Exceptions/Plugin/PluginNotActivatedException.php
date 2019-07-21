<?php

namespace Oforge\Engine\Modules\Core\Exceptions\Plugin;

use Exception;

/**
 * Class PluginNotActivatedException
 *
 * @package Oforge\Engine\Modules\Core\Exceptions
 */
class PluginNotActivatedException extends Exception {

    /**
     * PluginNotInstalledException constructor.
     *
     * @param string $pluginName
     */
    public function __construct(string $pluginName) {
        parent::__construct("The plugin '$pluginName' is not activated. You have to activate it first.");
    }

}
