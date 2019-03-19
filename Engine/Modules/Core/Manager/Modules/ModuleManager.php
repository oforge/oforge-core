<?php

namespace Oforge\Engine\Modules\Core\Manager\Modules;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;
use Noodlehaus\Exception;
use Oforge\Engine\Modules\Core\Abstracts\AbstractBootstrap;
use Oforge\Engine\Modules\Core\Bootstrap;
use Oforge\Engine\Modules\Core\Exceptions\ConfigElementAlreadyExists;
use Oforge\Engine\Modules\Core\Exceptions\CouldNotInstallModuleException;
use Oforge\Engine\Modules\Core\Helper\Helper;
use Oforge\Engine\Modules\Core\Helper\Statics;
use Oforge\Engine\Modules\Core\Models\Module\Module;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;

class ModuleManager {
    protected static $instance = null;

    public static function getInstance() {
        if (null === self::$instance) {
            self::$instance = new ModuleManager();
        }

        return self::$instance;
    }

    private $entityManger = null;
    private $moduleRepository = null;

    public function entityManger() : EntityManager {
        if (!isset($this->entityManger)) {
            $this->entityManger = Oforge()->DB()->getManager();
        }

        return $this->entityManger;
    }

    public function moduleRepository() : EntityRepository {
        if (!isset($this->moduleRepository)) {
            $this->moduleRepository = $this->entityManger()->getRepository(Module::class);
        }

        return $this->moduleRepository;
    }

    /**
     * Initialize all modules
     *
     * @throws CouldNotInstallModuleException
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     * @throws \Oforge\Engine\Modules\Core\Exceptions\ServiceNotFoundException
     * @throws \Oforge\Engine\Modules\Core\Exceptions\ServiceAlreadyDefinedException
     */
    public function init() {
        $startTime = microtime(true) * 1000;

        $files = Helper::getBootstrapFiles(ROOT_PATH . DIRECTORY_SEPARATOR . Statics::ENGINE_DIR . DIRECTORY_SEPARATOR);

        // init core module
        $this->initCoreModule(Bootstrap::class);

        // register all modules
        foreach ($files as $key => $dir) {

            // TODO: Check if suppressing error here is ok
            $fileMeta = @Helper::getFileMeta($dir);
            $this->register($fileMeta['namespace'] . "\\" . $fileMeta['class_name']);
        }
        // save all db changes
        $this->entityManger()->flush();

        // find all modules order by "order"
        $modules = $this->moduleRepository()->findBy(["active" => 1], ['order' => 'ASC']);

        // create working bucket with all modules that should be started
        $bucket = [];
        // add all modules except of the core bootstrap file
        foreach ($modules as $module) {
            /**
             * @var $module Module
             */
            $classname = $module->getName();
            $instance  = new $classname();
            if (get_class($instance) != Bootstrap::class) {
                array_push($bucket, $instance);
            }
        }

        // create array with all installed module bootstrap classes
        $installed = [Bootstrap::class => true];

        // installed bootstrap
        $count = 0;
        do {
            $trash = [];
            for ($i = 0; $i < sizeof($bucket); $i++) {
                /**
                 * @var $instance AbstractBootstrap
                 */
                $instance = $bucket[$i];


                $startTime = microtime(true) * 1000 ;


                if (sizeof($instance->getDependencies()) > 0) {
                    $found = true;

                    foreach ($instance->getDependencies() as $dependency) {
                        if (!array_key_exists($dependency, $installed) || !$installed[$dependency]) {
                            $found = false;
                            break;
                        }
                    }

                    if ($found) {
                        $classname = get_class($instance);
                        $this->initModule(get_class($instance));
                        $installed[$classname] = true;
                    } else {
                        array_push($trash, $instance);
                    }

                } else {
                    $classname = get_class($instance);
                    $this->initModule(get_class($instance));
                    $installed[$classname] = true;
                }
            }

            $bucket = $trash;
            if ($count++ > 10) {
                break;
            }

        } while (sizeof($bucket) > 0);  // do it until everything is installed

        if (sizeof($bucket) > 0) {
            throw new CouldNotInstallModuleException(get_class($bucket[0]), $bucket[0]->getDependencies());
        }
    }

    /**
     * Initialize the core module
     *
     * @param $className
     *
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     * @throws \Oforge\Engine\Modules\Core\Exceptions\ServiceAlreadyDefinedException
     * @throws \Oforge\Engine\Modules\Core\Exceptions\ServiceNotFoundException
     */
    private function initCoreModule($className) {
        $startTime = microtime(true) * 1000;

        if (is_subclass_of($className, AbstractBootstrap::class)) {
            /**
             * @var $instance AbstractBootstrap
             */
            $instance = new $className();

            Oforge()->DB()->initSchema($instance->getModels());

            $services = $instance->getServices();
            Oforge()->Services()->register($services);

            $endpoints = $instance->getEndpoints();
            Oforge()->Services()->get("endpoints")->register($endpoints);

            /**
             * @var $entry Module
             */
            $entry = $this->moduleRepository()->findOneBy(["name" => $className]);

            $needFlush = false;
            if (isset($entry) && !$entry->getInstalled()) {
                try {
                    $instance->install();
                } catch (ConfigElementAlreadyExists $e) {
                }
                $this->entityManger()->persist($entry->setInstalled(true));
                $needFlush = true;
            } elseif (!isset($entry)) {
                $this->register($className);
                try {
                    $instance->install();
                } catch (ConfigElementAlreadyExists $e) {
                }
                $entry = $this->moduleRepository()->findOneBy(["name" => $className]);
                $this->entityManger()->persist($entry->setInstalled(true));
                $needFlush = true;
            }

            $instance->activate();

            if ($needFlush) {
                $this->entityManger()->flush();
            }
        }
    }

    /**
     * Register a module.
     * This means: if a module isn't found in the db table, insert it
     *
     * @param $className
     *
     * @throws \Doctrine\ORM\ORMException
     */
    protected function register($className) {
        if (is_subclass_of($className, AbstractBootstrap::class)) {
            /**
             * @var $instance AbstractBootstrap
             */
            $instance = new $className();

            $moduleEntry = $this->moduleRepository()->findBy(["name" => get_class($instance)]);
            if (isset($moduleEntry) && sizeof($moduleEntry) > 0) {
                //found -> nothing to do;
            } else { // if not put the data into the database
                $newEntry = Module::create(["name" => get_class($instance), "order" => $instance->getOrder(), "active" => 1, "installed" => 0]);
                $this->entityManger()->persist($newEntry);
            }
        }
    }

    /**
     * Initialize a module
     *
     * @param $className
     *
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     * @throws \Oforge\Engine\Modules\Core\Exceptions\ServiceAlreadyDefinedException
     * @throws \Oforge\Engine\Modules\Core\Exceptions\ServiceNotFoundException
     */
    protected function initModule($className) {
        if (is_subclass_of($className, AbstractBootstrap::class)) {
            /**
             * @var $instance AbstractBootstrap
             */
            $instance = new $className();

            Oforge()->DB()->initSchema($instance->getModels());

            $services = $instance->getServices();
            Oforge()->Services()->register($services);

            $endpoints = $instance->getEndpoints();
            Oforge()->Services()->get("endpoints")->register($endpoints);

            $middleware = $instance->getMiddleware();
            Oforge()->Services()->get("middleware")->registerFromModule($middleware);

            /**
             * @var $entry Module
             */
            $entry = $this->moduleRepository()->findOneBy(["name" => $className]);

            if (isset($entry) && !$entry->getInstalled()) {
                try {
                    $instance->install();
                } catch (ConfigElementAlreadyExists $e) {
                }
                $this->entityManger()->persist($entry->setInstalled(true));
            }

            $instance->activate();

            $this->entityManger()->flush();
        }
    }
}
