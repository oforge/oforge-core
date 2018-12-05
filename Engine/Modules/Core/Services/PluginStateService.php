<?php
namespace Oforge\Engine\Modules\Core\Services;

use Doctrine\Common\Persistence\ObjectRepository;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;
use Oforge\Engine\Modules\Core\Abstracts\AbstractBootstrap;
use Oforge\Engine\Modules\Core\Exceptions\CouldNotActivatePluginException;
use Oforge\Engine\Modules\Core\Exceptions\CouldNotDeactivatePluginException;
use Oforge\Engine\Modules\Core\Exceptions\PluginNotFoundException;
use Oforge\Engine\Modules\Core\Helper\Helper;
use Oforge\Engine\Modules\Core\Models\Plugin\Plugin;

class PluginStateService
{
    /**
     * @var $em EntityManager
     */
    private $em;
    
    /**
     * @var $repo ObjectRepository|EntityRepository
     */
    private $repo;
    
    public function __construct() {
        $this->em = Oforge()->DB()->getManager();
        $this->repo = $this->em->getRepository(Plugin::class);
    }
    
    /**
     * Initialize an active plugin
     *
     * @param $pluginName
     *
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     * @throws \Oforge\Engine\Modules\Core\Exceptions\InvalidClassException
     * @throws \Oforge\Engine\Modules\Core\Exceptions\ServiceAlreadyDefinedException
     * @throws \Oforge\Engine\Modules\Core\Exceptions\ServiceNotFoundException
     */
    public function initPlugin($pluginName) {
        /**
         * @var $plugin Plugin
         */
        $plugin = $this->repo->findOneBy(["name" => $pluginName]);
        
        if (isset($plugin) && $plugin->getActive()) {
            $instance = Helper::getBootstrapInstance($pluginName);
            if (isset($instance)) {
                Oforge()->DB()->initSchema($instance->getModels());
                $services = $instance->getServices();
                Oforge()->Services()->register($services);
                $endpoints = $instance->getEndpoints();
                
                /**
                 * @var $endpointService EndpointService
                 */
                $endpointService = Oforge()->Services()->get("endpoints");
                $endpointService->register($endpoints);
            }
        }
    }
    
    /**
     * Register the plugin in the core.
     * The plugin gets stored in a database table but doesn't get activated or "installed"
     *
     * @param $pluginName
     *
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     * @throws \Oforge\Engine\Modules\Core\Exceptions\ServiceNotFoundException
     * @throws \Oforge\Engine\Modules\Core\Exceptions\InvalidClassException
     */
    public function register($pluginName) {
        /**
         * @var $plugin Plugin
         */
        $plugin = $this->repo->findOneBy(["name" => $pluginName]);
        if (!isset($plugin)) {
            $instance = Helper::getBootstrapInstance($pluginName);
            if (isset($instance)) {
                $pluginMiddlewares = $instance->getMiddleware();
                $plugin = Plugin::create(Plugin::class, array("name" => $pluginName, "active" => 0, "installed" => 0, "order" => $instance->getOrder()));
                $this->em->persist($plugin);
                $this->em->flush();
                if(isset($pluginMiddlewares) && is_array($pluginMiddlewares) && sizeof($pluginMiddlewares) > 0) {
                    
                    /**
                     * @var $middlewaresService MiddlewareService
                     */
                    $middlewaresService = Oforge()->Services()->get("middleware");
                    $middlewares = $middlewaresService->register($pluginMiddlewares, $plugin);
                    $plugin->setMiddlewares($middlewares);
                }
                $this->em->persist($plugin);
                $this->em->flush();
            }
        }
    }
    
    /**
     * check if the plugin is installed
     * check if the plugin has a separate model or changes a model / schema
     * install
     *
     * @param string $pluginName
     *
     * @throws PluginNotFoundException
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     * @throws \Oforge\Engine\Modules\Core\Exceptions\InvalidClassException
     * @throws \Oforge\Engine\Modules\Core\Exceptions\ServiceAlreadyDefinedException
     */
    public function install(string $pluginName) {
        /**
         * @var $plugin Plugin
         */
        $plugin = $this->repo->findOneBy(['name' => $pluginName]);
        if (!isset($plugin)) {
            throw new PluginNotFoundException($pluginName);
        }
        $instance = Helper::getBootstrapInstance($pluginName);
        if (isset($instance)) {
            $models = $instance->getModels();
            if (sizeof($models) > 0) {
                Oforge()->DB()->initSchema($models);
            }
            $services = $instance->getServices();
            Oforge()->Services()->register($services);
            $instance->install();
            $plugin->setInstalled(true);
            $this->em->persist($plugin);
            $this->em->flush();
        }
    }
    
    /**
     * @param string $pluginName
     *
     * @throws CouldNotDeactivatePluginException
     * @throws PluginNotFoundException
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     * @throws \Oforge\Engine\Modules\Core\Exceptions\InvalidClassException
     */
    public function uninstall(string $pluginName) {
        // First deactivate plugin
        echo "try to uninstall<br>";
        $this->deactivate($pluginName);
        
        /**
         * @var $plugin Plugin
         */
        $plugin = $this->repo->findOneBy(['name' => $pluginName]);
        if (!isset($plugin)) {
            throw new PluginNotFoundException($pluginName);
        }
        $plugin->setInstalled(false);
        $this->em->persist($plugin);
        $this->em->flush();
    }
    
    /**
     * If dependencies aren't installed, throw
     * else
     * activate
     *
     * @param $pluginName
     *
     * @throws CouldNotActivatePluginException
     * @throws \Oforge\Engine\Modules\Core\Exceptions\InvalidClassException
     * @throws \Doctrine\ORM\ORMException
     * @throws \ReflectionException
     */
    public function activate($pluginName) {
        /**
         * @var $plugin Plugin
         */
        $plugin = $this->repo->findOneBy(['name' => $pluginName]);
        $instance = Helper::getBootstrapInstance($pluginName);
 
        if (sizeof($instance->getDependencies()) > 0) {
            foreach ($instance->getDependencies() as $dependency) {
                if (is_subclass_of($dependency, AbstractBootstrap::class )) {
                    $dependencyName = (new \ReflectionClass($dependency))->getNamespaceName();
                    $dependencyActiveAndInstalled = $this->repo->findOneBy(["name" => $dependencyName, "active" => 1, "installed" => 1]);
                    
                    if (!isset($dependencyActiveAndInstalled)) {
                        throw new CouldNotActivatePluginException($pluginName, [$dependencyName]);
                    }
                }
            }
        }

        if(sizeof($plugin->getMiddlewares()) > 0) {
            foreach ($plugin->getMiddlewares() as $middleware) {
                $middleware->setActive(true);
            }
        }

        $plugin->setActive(true);
        $this->em->persist($plugin);
        $this->em->flush();
    }
    
    /**
     * Deactivate a plugin, if it has no dependants that are active
     *
     * @param $pluginName
     *
     * @throws CouldNotDeactivatePluginException
     * @throws PluginNotFoundException
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     * @throws \Oforge\Engine\Modules\Core\Exceptions\InvalidClassException
     */
    public function deactivate($pluginName) {
        /**
         * @var $pluginToDeactivate Plugin
         */
        $pluginToDeactivate = $this->repo->findOneBy(["name" => $pluginName]);
        
        /**
         * @var $plugins Plugin[]
         */
        $plugins = $this->repo->findBy(["active" => 1]);
        
        if (!isset($pluginToDeactivate)) {
            throw new PluginNotFoundException($pluginName);
        }
        $dependants = [];
        foreach($plugins as $plugin) {
            $instance = Helper::getBootstrapInstance($plugin->getName());
            if (isset($instance) && sizeof($instance->getDependencies()) > 0) {
               
                if(in_array($pluginName . "\\Bootstrap", $instance->getDependencies()))  {
                    array_push($dependants, $plugin->getName());
                }
            }
        }
        if (sizeof($dependants) > 0) {
            // (╯°□°）╯︵ ┻━┻
            throw new CouldNotDeactivatePluginException($pluginName, $dependants);
        }

        if(sizeof($pluginToDeactivate->getMiddlewares()) > 0) {
            foreach ($pluginToDeactivate->getMiddlewares() as $middleware) {
                $middleware->setActive(false);
            }
        }

        $pluginToDeactivate->setActive(false);
        $this->em->persist($pluginToDeactivate);
        $this->em->flush();
    }
    
    /**
     * Update a plugin
     */
    public function update() {
        //TODO implement update function ¯\_(ツ)_/¯
    }
}
