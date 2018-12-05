<?php

namespace Oforge\Engine\Modules\Core\Manager\Modules;

use Noodlehaus\Exception;
use Oforge\Engine\Modules\Core\Abstracts\AbstractBootstrap;
use Oforge\Engine\Modules\Core\Bootstrap;
use Oforge\Engine\Modules\Core\Exceptions\CouldNotInstallModuleException;
use Oforge\Engine\Modules\Core\Helper\Helper;
use Oforge\Engine\Modules\Core\Helper\Statics;
use Oforge\Engine\Modules\Core\Models\Module\Module;

class ModuleManager
{
    protected static $instance = null;
    protected $em = null;
    protected $entryRepository = null;
    protected $moduleRepository = null;

    protected function __construct()
    {
        $this->em = Oforge()->DB()->getManager();
        $this->moduleRepository = $this->em->getRepository(Module::class);
    }

    public static function getInstance()
    {
        if (null === self::$instance) {
            self::$instance = new ModuleManager();
        }
        return self::$instance;
    }

    /**
     * Initialize all modules
     *
     * @throws CouldNotInstallModuleException
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     * @throws \Oforge\Engine\Modules\Core\Exceptions\ServiceNotFoundException
     */
    public function init()
    {
        $files = Helper::getBootstrapFiles(ROOT_PATH . DIRECTORY_SEPARATOR . Statics::ENGINE_DIR);

        // init core module
        $this->initCoreModule(Bootstrap::class);

        // register all modules
        foreach ($files as $key => $dir) {
            $this->register("\\Oforge\\Engine\\Modules\\" . $key . "\\Bootstrap");
        }

        // save all db changes
        $this->em->flush();

        // find all modules order by "order"
        $modules = $this->moduleRepository->findBy(array("active" => 1), array('order' => 'ASC'));

        // create working bucket with all modules that should be started
        $bucket = [];
        // add all modules except of the core bootstrap file
        foreach ($modules as $module) {
            /**
             * @var $module Module
             */
            $classname = $module->getName();
            $instance = new $classname();
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
     * @throws \Oforge\Engine\Modules\Core\Exceptions\ServiceNotFoundException
     */
    private function initCoreModule($className)
    {
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
            $entry = $this->moduleRepository->findOneBy(["name" => $className]);

            if (isset($entry) && !$entry->getInstalled()) {
                $instance->install();
                $this->em->persist($entry->setInstalled(true));
            } else if (!isset($entry)) {
                $this->register($className);
                $instance->install();
                $entry = $this->moduleRepository->findOneBy(["name" => $className]);
                $this->em->persist($entry->setInstalled(true));
            }

            $this->em->flush();
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
    protected function register($className)
    {
        if (is_subclass_of($className, AbstractBootstrap::class)) {
            /**
             * @var $instance AbstractBootstrap
             */
            $instance = new $className();

            $moduleEntry = $this->moduleRepository->findBy(["name" => get_class($instance)]);
            if (isset($moduleEntry) && sizeof($moduleEntry) > 0) {
                //found -> nothing to do;
            } else { // if not put the data into the database
                $newEntry = Module::create(Module::class, ["name" => get_class($instance), "order" => $instance->getOrder(), "active" => 1, "installed" => 0]);
                $this->em->persist($newEntry);
            }
        }
    }

    /**
     * Initialize a module
     *
     * @param $className
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     * @throws \Oforge\Engine\Modules\Core\Exceptions\ServiceNotFoundException
     */
    protected function initModule($className)
    {
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
            $entry = $this->moduleRepository->findOneBy(["name" => $className]);

            if (isset($entry) && !$entry->getInstalled()) {
                $instance->install();
                $this->em->persist($entry->setInstalled(true));
            }

            $this->em->flush();
        }
    }
}
