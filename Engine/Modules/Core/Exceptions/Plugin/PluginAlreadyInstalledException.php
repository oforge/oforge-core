<?php

namespace Oforge\Engine\Modules\Core\Exceptions\Plugin;

use Exception;

/**
 * Class PluginAlreadyInstalledException
 *
 * @package Oforge\Engine\Modules\Core\Exceptions
 */
class PluginAlreadyInstalledException extends Exception {

    /**
     * PluginNotInstalledException constructor.
     *
     * @param string $pluginName
     */
    public function __construct(string $pluginName) {
        parent::__construct("The plugin '$pluginName' is already installed. You cannot install it twice.");
    }

}
