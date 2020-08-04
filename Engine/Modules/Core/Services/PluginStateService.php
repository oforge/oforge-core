<?php

namespace Oforge\Engine\Modules\Core\Services;

use Doctrine\Common\Annotations\AnnotationException;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use Oforge\Engine\Modules\Core\Abstracts\AbstractBootstrap;
use Oforge\Engine\Modules\Core\Abstracts\AbstractDatabaseAccess;
use Oforge\Engine\Modules\Core\Exceptions\ConfigOptionKeyNotExistException;
use Oforge\Engine\Modules\Core\Exceptions\InvalidClassException;
use Oforge\Engine\Modules\Core\Exceptions\Plugin\CouldNotActivatePluginException;
use Oforge\Engine\Modules\Core\Exceptions\Plugin\CouldNotDeactivatePluginException;
use Oforge\Engine\Modules\Core\Exceptions\Plugin\CouldNotInstallPluginException;
use Oforge\Engine\Modules\Core\Exceptions\Plugin\PluginAlreadyActivatedException;
use Oforge\Engine\Modules\Core\Exceptions\Plugin\PluginAlreadyInstalledException;
use Oforge\Engine\Modules\Core\Exceptions\Plugin\PluginNotActivatedException;
use Oforge\Engine\Modules\Core\Exceptions\Plugin\PluginNotFoundException;
use Oforge\Engine\Modules\Core\Exceptions\Plugin\PluginNotInstalledException;
use Oforge\Engine\Modules\Core\Exceptions\ServiceAlreadyExistException;
use Oforge\Engine\Modules\Core\Exceptions\ServiceNotFoundException;
use Oforge\Engine\Modules\Core\Exceptions\Template\TemplateNotFoundException;
use Oforge\Engine\Modules\Core\Helper\Helper;
use Oforge\Engine\Modules\Core\Models\Plugin\Plugin;
use Oforge\Engine\Modules\TemplateEngine\Core\Exceptions\InvalidScssVariableException;
use Oforge\Engine\Modules\TemplateEngine\Core\Services\TemplateManagementService;
use ReflectionClass;
use ReflectionException;

/**
 * Class PluginStateService
 *
 * @package Oforge\Engine\Modules\Core\Services
 */
class PluginStateService extends AbstractDatabaseAccess {

    public function __construct() {
        parent::__construct(Plugin::class);
    }

    /**
     * Initialize an active plugin
     *
     * @param $pluginName
     *
     * @throws InvalidClassException
     * @throws ORMException
     * @throws OptimisticLockException
     * @throws InvalidClassException
     * @throws ServiceAlreadyExistException
     * @throws ServiceNotFoundException
     * @throws AnnotationException
     */
    public function initPlugin($pluginName) {
        /** @var Plugin $plugin */
        $plugin = $this->repository()->findOneBy(['name' => $pluginName]);

        if (isset($plugin) && $plugin->getActive()) {
            $instance = Helper::getBootstrapInstance($pluginName);
            if (isset($instance)) {
                Oforge()->DB()->initModelSchema($instance->getModels());
                $services = $instance->getServices();
                Oforge()->Services()->register($services);
                $endpoints = $instance->getEndpoints();

                /** @var EndpointService $endpointService */
                $endpointService = Oforge()->Services()->get('endpoint');
                $endpointService->install($endpoints);//TODO coreRafactoring
                $endpointService->activate($endpoints);//TODO coreRafactoring
                //   $instance->load();
            }
        }
    }

    /**
     * Register the plugin in the core.
     * The plugin gets stored in a database table but doesn't get activated or 'installed'
     *
     * @param $pluginName
     *
     * @throws ConfigOptionKeyNotExistException
     * @throws InvalidClassException
     * @throws ORMException
     * @throws ServiceNotFoundException
     */
    public function register($pluginName) {
        /** @var Plugin $plugin */
        $plugin = $this->repository()->findOneBy(['name' => $pluginName]);

        if (!isset($plugin)) {
            $instance = Helper::getBootstrapInstance($pluginName);
            if (isset($instance)) {
                $plugin = Plugin::create([
                    'name'      => $pluginName,
                    'active'    => false,
                    'installed' => false,
                    'order'     => $instance->getOrder(),
                ]);

                $this->entityManager()->create($plugin);
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
     * @throws PluginAlreadyInstalledException
     * @throws CouldNotInstallPluginException
     * @throws ORMException
     * @throws OptimisticLockException
     * @throws InvalidClassException
     * @throws ServiceAlreadyExistException
     * @throws ReflectionException
     */
    public function install(string $pluginName) {
        /** @var Plugin $plugin */
        $plugin = $this->repository()->findOneBy(['name' => $pluginName]);

        if (!isset($plugin)) {
            throw new PluginNotFoundException($pluginName);
        }

        if ($plugin->getInstalled()) {
            throw new PluginAlreadyInstalledException($pluginName);
        }
        /** @var AbstractBootstrap $instance */
        $instance = Helper::getBootstrapInstance($pluginName);

        if (isset($instance)) {
            $models = $instance->getModels();
            if (!empty($models)) {
                Oforge()->DB()->initModelSchema($models);
            }

            if (!empty($instance->getDependencies())) {
                foreach ($instance->getDependencies() as $dependency) {
                    if (is_subclass_of($dependency, AbstractBootstrap::class)) {
                        $dependencyName               = (new ReflectionClass($dependency))->getNamespaceName();
                        $dependencyActiveAndInstalled = $this->repository()->findOneBy(['name' => $dependencyName, 'installed' => 1]);

                        if (!isset($dependencyActiveAndInstalled)) {
                            throw new CouldNotInstallPluginException($pluginName, [$dependencyName]);
                        }
                    }
                }
            }

            if ($plugin->getInstalled() === false) {
                $services = $instance->getServices();
                Oforge()->Services()->register($services);

                /** @var MiddlewareService $middlewaresService */
                $middlewaresService = Oforge()->Services()->get('middleware');
                $middlewaresService->install($instance->getMiddlewares(), false);

                $instance->install();
                $plugin->setInstalled(true);
                $this->entityManager()->update($plugin);
            }
        }
    }

    /**
     * @param string $pluginName
     * @param bool $keepData
     *
     * @throws CouldNotDeactivatePluginException
     * @throws InvalidClassException
     * @throws ORMException
     * @throws OptimisticLockException
     * @throws PluginNotActivatedException
     * @throws PluginNotFoundException
     * @throws PluginNotInstalledException
     * @throws ServiceNotFoundException
     */
    public function uninstall(string $pluginName, bool $keepData) {
        /** @var Plugin $plugin */
        $plugin = $this->repository()->findOneBy(['name' => $pluginName]);

        if (!isset($plugin)) {
            throw new PluginNotFoundException($pluginName);
        }

        if (!$plugin->getInstalled()) {
            throw new PluginNotInstalledException($pluginName);
        }

        if ($plugin->getActive() === true) {
            // First deactivate plugin
            $this->deactivate($pluginName);
        }
        /** @var AbstractBootstrap $instance */
        $instance = Helper::getBootstrapInstance($pluginName);

        if (!$keepData) {
            //TODO remove Data (tables etc)
        }
        /** @var MiddlewareService $middlewaresService */
        $middlewaresService = Oforge()->Services()->get('middleware');
        $middlewaresService->uninstall($instance->getMiddlewares());

        if (isset($instance)) {
            $instance->uninstall($keepData);
        }

        $plugin->setInstalled(false);
        $this->entityManager()->update($plugin);
    }

    /**
     * If dependencies aren't installed, throw
     * else
     * activate
     *
     * @param $pluginName
     *
     * @throws PluginNotFoundException
     * @throws PluginNotInstalledException
     * @throws PluginAlreadyActivatedException
     * @throws CouldNotActivatePluginException
     * @throws TemplateNotFoundException
     * @throws InvalidScssVariableException
     * @throws InvalidClassException
     * @throws ORMException
     * @throws OptimisticLockException
     * @throws ReflectionException
     * @throws ServiceAlreadyExistException
     * @throws ServiceNotFoundException
     */
    public function activate($pluginName) {
        /** @var Plugin $plugin */
        $plugin = $this->repository()->findOneBy(['name' => $pluginName]);

        if (!isset($plugin)) {
            throw new PluginNotFoundException($pluginName);
        }
        if (!$plugin->getInstalled()) {
            throw new PluginNotInstalledException($pluginName);
        }

        if ($plugin->getActive()) {
            throw new PluginAlreadyActivatedException($pluginName);
        }
        $instance = Helper::getBootstrapInstance($pluginName);

        if (!isset($instance)) {
            throw new PluginNotFoundException($pluginName);
        }

        if (!empty($instance->getDependencies())) {
            foreach ($instance->getDependencies() as $dependency) {
                if (is_subclass_of($dependency, AbstractBootstrap::class)) {
                    $dependencyName               = (new ReflectionClass($dependency))->getNamespaceName();
                    $dependencyActiveAndInstalled = $this->repository()->findOneBy(['name' => $dependencyName, 'active' => 1, 'installed' => 1]);

                    if (!isset($dependencyActiveAndInstalled)) {
                        throw new CouldNotActivatePluginException($pluginName, [$dependencyName]);
                    }
                }
            }
        }

        if ($plugin->getActive() === false) {
            $services = $instance->getServices();
            Oforge()->Services()->register($services);
            /** @var MiddlewareService $middlewareService */
            $middlewareService = Oforge()->Services()->get('middleware');
            $middlewareService->activate($instance->getMiddlewares());
            $instance->activate();
            $plugin->setActive(true);
            $this->entityManager()->update($plugin);
        }
        //$instance->load(); TODO: MS testing
        /** @var TemplateManagementService $templateManagementService */
        $templateManagementService = Oforge()->Services()->get('template.management');
        $templateManagementService->build();
    }

    /**
     * Deactivate a plugin, if it has no dependants that are active
     *
     * @param $pluginName
     *
     * @throws PluginNotFoundException
     * @throws PluginNotInstalledException
     * @throws PluginNotActivatedException
     * @throws CouldNotDeactivatePluginException
     * @throws ORMException
     * @throws OptimisticLockException
     * @throws InvalidClassException
     * @throws ServiceNotFoundException
     */
    public function deactivate($pluginName) {
        /** @var Plugin $pluginToDeactivate */
        $pluginToDeactivate = $this->repository()->findOneBy(['name' => $pluginName]);

        if (!isset($pluginToDeactivate)) {
            throw new PluginNotFoundException($pluginName);
        }

        if (!$pluginToDeactivate->getInstalled()) {
            throw new PluginNotInstalledException($pluginName);
        }

        if (!$pluginToDeactivate->getActive()) {
            throw new PluginNotActivatedException($pluginName);
        }

        /** @var Plugin[] $plugins */
        $plugins = $this->repository()->findBy(['active' => 1]);
        /** @var AbstractBootstrap $pluginToDeactivateInstance */
        $pluginToDeactivateInstance = Helper::getBootstrapInstance($pluginName);

        $dependants = [];
        foreach ($plugins as $plugin) {
            $instance = Helper::getBootstrapInstance($plugin->getName());
            if (isset($instance) && sizeof($instance->getDependencies()) > 0) {
                if (in_array($pluginName . '\\Bootstrap', $instance->getDependencies())) {
                    array_push($dependants, $plugin->getName());
                }
            }
        }
        if (!empty($dependants)) {
            // (╯°□°）╯︵ ┻━┻
            throw new CouldNotDeactivatePluginException($pluginName, $dependants);
        }

        if ($pluginToDeactivate->getActive() === true) {
            if (isset($pluginToDeactivateInstance)) {
                /** @var MiddlewareService $middlewareService */
                $middlewareService = Oforge()->Services()->get('middleware');
                $middlewareService->deactivate($pluginToDeactivateInstance->getMiddlewares());
                $pluginToDeactivateInstance->deactivate();
            }

            $pluginToDeactivate->setActive(false);
            $this->entityManager()->update($pluginToDeactivate);
        }
    }

    /**
     * Call Method on Plugin Bootstrap file.
     *
     * @param string $action One of: install|activate
     * @param string $pluginName
     *
     * @throws InvalidClassException
     */
    public function callPluginBootstrapMethod(string $action, string $pluginName) {
        $actions = ['install'/*, 'uninstall'*/, 'activate'];
        if (in_array($action, $actions)) {
            /** @var Plugin $plugin */
            $plugin = $this->repository()->findOneBy(['name' => $pluginName]);
            if ($plugin->getInstalled() && $plugin->getActive()) {
                $instance = Helper::getBootstrapInstance($pluginName);
                if ($instance === null) {
                    echo "Plugin Bootstrap not found.\n";
                } else {
                    $instance->$action();
                }
            } else {
                echo "Actions could only be called on installed & active plugins.\n";
            }
        } else {
            echo "Unsupported action '$action'. Must be of: ", implode(', ', $actions), "\n";
        }
    }

}
