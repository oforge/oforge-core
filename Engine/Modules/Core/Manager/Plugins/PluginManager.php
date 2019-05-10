<?php

namespace Oforge\Engine\Modules\Core\Manager\Plugins;

use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use Oforge\Engine\Modules\Core\Abstracts\AbstractDatabaseAccess;
use Oforge\Engine\Modules\Core\Exceptions\ConfigOptionKeyNotExistException;
use Oforge\Engine\Modules\Core\Exceptions\InvalidClassException;
use Oforge\Engine\Modules\Core\Exceptions\Plugin\CouldNotInstallPluginException;
use Oforge\Engine\Modules\Core\Exceptions\ServiceAlreadyExistException;
use Oforge\Engine\Modules\Core\Exceptions\ServiceNotFoundException;
use Oforge\Engine\Modules\Core\Helper\Helper;
use Oforge\Engine\Modules\Core\Helper\Statics;
use Oforge\Engine\Modules\Core\Models\Plugin\Plugin;
use Oforge\Engine\Modules\Core\Services\PluginStateService;

class PluginManager extends AbstractDatabaseAccess {
    protected static $instance = null;

    public static function getInstance() {
        if (null === self::$instance) {
            self::$instance = new PluginManager(['default' => Plugin::class]);
        }

        return self::$instance;
    }

    /**
     * Initialize the PluginManager. Register all plugins
     *
     * @throws ConfigOptionKeyNotExistException
     * @throws CouldNotInstallPluginException
     * @throws InvalidClassException
     * @throws ORMException
     * @throws OptimisticLockException
     * @throws ServiceAlreadyExistException
     * @throws ServiceNotFoundException
     */
    public function init() {
        $pluginFiles = Helper::getBootstrapFiles(ROOT_PATH . DIRECTORY_SEPARATOR . Statics::PLUGIN_DIR);

        /**
         * @var $pluginService PluginStateService
         */
        $pluginService = Oforge()->Services()->get("plugin.state");

        foreach ($pluginFiles as $pluginName => $dir) {
            // TODO: check if suppressing error message here is ok
            $fileMeta = @Helper::getFileMeta($dir);
            $pluginService->register($fileMeta['namespace']);
        }

        //find all plugins order by "order"
        $plugins = $this->repository()->findBy(["active" => 1], ['order' => 'ASC']);
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
        $count     = 0;

        do {
            $trash = [];
            for ($i = 0; $i < sizeof($bucket); $i++) {
                /**
                 * @var $instance ["instance"] AbstractBootstrap
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
