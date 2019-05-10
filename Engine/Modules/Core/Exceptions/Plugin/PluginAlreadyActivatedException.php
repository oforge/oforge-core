<?php

namespace Oforge\Engine\Modules\Core\Exceptions\Plugin;

use Exception;

/**
 * Class PluginAlreadyActivatedException
 *
 * @package Oforge\Engine\Modules\Core\Exceptions\Plugin
 */
class PluginAlreadyActivatedException extends Exception {

    /**
     * PluginNotInstalledException constructor.
     *
     * @param string $pluginName
     */
    public function __construct(string $pluginName) {
        parent::__construct("The plugin '$pluginName' is already activated. You cannot activate it twice.");
    }

}
