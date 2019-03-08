<?php

namespace Oforge\Engine\Modules\Core\Manager\Plugins;

use Oforge\Engine\Modules\Core\Abstracts\AbstractBootstrap;
use Oforge\Engine\Modules\Core\Exceptions\CouldNotInstallPluginException;
use Oforge\Engine\Modules\Core\Helper\Helper;
use Oforge\Engine\Modules\Core\Helper\Statics;
use Oforge\Engine\Modules\Core\Models\Plugin\Plugin;
use Oforge\Engine\Modules\Core\Services\PluginStateService;

class PluginManager
{
    protected static $instance = null;

    public static function getInstance()
    {
        if (null === self::$instance) {
            self::$instance = new PluginManager();
        }
        return self::$instance;
    }

    /**
     * Initialize the pluginmanager. Register all plugins
     *
     * @throws CouldNotInstallPluginException
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     * @throws \Oforge\Engine\Modules\Core\Exceptions\InvalidClassException
     * @throws \Oforge\Engine\Modules\Core\Exceptions\ServiceNotFoundException
     */
    public function init()
    {
        $pluginFiles = Helper::getBootstrapFiles(ROOT_PATH . DIRECTORY_SEPARATOR . Statics::PLUGIN_DIR);

        /**
         * @var $pluginService PluginStateService
         */
        $pluginService = Oforge()->Services()->get("plugin.state");

        foreach ($pluginFiles as $pluginName => $dir) {
            $pluginService->register("\\" . $pluginName);
        }
        $em = Oforge()->DB()->getManager();
        $pluginRepository = $em->getRepository(Plugin::class);

        //find all plugins order by "order"
        $plugins = $pluginRepository->findBy(array("active" => 1), array('order' => 'ASC'));
        //create working bucket with all plugins that should be started
        $bucket = [];

        /**
         * @var $plugins Plugin[]
         */
        foreach ($plugins as $plugin) {
            $classname = $plugin->getName() . "\\Bootstrap";
            array_push($bucket, ["instance" => new $classname(), "name" => $plugin->getName()]);
        }

        // create array with all installed plugin classes
        $installed = [];
        $count = 0;

        do {
            $trash = [];
            for ($i = 0; $i < sizeof($bucket); $i++) {

                /**
                 * @var $instance["instance"] AbstractBootstrap
                 */
                $instance = $bucket[$i];
                if (sizeof($instance["instance"]->getDependencies()) > 0) {
                    $found = true;

                    foreach ($instance["instance"]->getDependencies() as $dependency) {
                        if (!array_key_exists($dependency, $installed) || !$installed[$dependency]) {
                            $found = false;
                            break;
                        }
                    }
                    if ($found) {
                        $classname = get_class($instance["instance"]);
                        $pluginService->initPlugin($instance["name"]);
                        $installed[$classname] = true;
                    } else {
                        array_push($trash, $instance);
                    }
                } else {
                    $classname = get_class($instance["instance"]);
                    $pluginService->initPlugin($instance["name"]);
                    $installed[$classname] = true;
                }
            }

            $bucket = $trash;
            if ($count++ > 10) {
                break;
            }
        } while (sizeof($bucket) > 0);  // do it until everything is installed

        if (sizeof($bucket) > 0) {
            throw new CouldNotInstallPluginException(get_class($bucket[0]), $bucket[0]->getDependencies());
        }
    }
}
