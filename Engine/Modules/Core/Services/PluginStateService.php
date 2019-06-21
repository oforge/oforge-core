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
     * @throws OptimisticLockException
     * @throws ServiceNotFoundException
     */
    public function register($pluginName) {
        /** @var Plugin $plugin */
        $plugin = $this->repository()->findOneBy(['name' => $pluginName]);

        if (!isset($plugin)) {
            $instance = Helper::getBootstrapInstance($pluginName);
            if (isset($instance)) {
                $pluginMiddlewares = $instance->getMiddlewares();
                $plugin            = Plugin::create([
                    'name'      => $pluginName,
                    'active'    => false,
                    'installed' => false,
                    'order'     => $instance->getOrder(),
                ]);

                $this->entityManager()->create($plugin);

                if (isset($pluginMiddlewares) && is_array($pluginMiddlewares) && sizeof($pluginMiddlewares) > 0) {
                    /** @var MiddlewareService $middlewaresService */
                    $middlewaresService = Oforge()->Services()->get('middleware');
                    $middlewaresService->register($pluginMiddlewares, false);
                }
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

        $instance = Helper::getBootstrapInstance($pluginName);

        if (isset($instance)) {
            $models = $instance->getModels();
            if (sizeof($models) > 0) {
                Oforge()->DB()->initModelSchema($models);
            }

            if (sizeof($instance->getDependencies()) > 0) {
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
                $instance->install();
                $plugin->setInstalled(true);
                $this->entityManager()->update($plugin);
            }
        }
    }

    /**
     * @param string $pluginName
     *
     * @throws PluginNotFoundException
     * @throws PluginNotInstalledException
     * @throws PluginNotActivatedException
     * @throws CouldNotDeactivatePluginException
     * @throws InvalidClassException
     * @throws ORMException
     * @throws OptimisticLockException
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

        $instance = Helper::getBootstrapInstance($pluginName);

        if (!$keepData) {
            //TODO remove Data (tables etc)
        }

        if (isset($instance)) {
            $instance->uninstall();
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

        if (sizeof($instance->getDependencies()) > 0) {
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

        if (sizeof($instance->getMiddlewares()) > 0) {
            /** @var MiddlewareService $middlewareService */
            $middlewareService = Oforge()->Services()->get('middleware');
            $middlewareNames   = [];
            foreach ($instance->getMiddlewares() as $middleware) {
                $middlewareNames = array_merge($middlewareNames, $this->getMiddlewareNames($middleware));
            }

            foreach ($middlewareNames as $middlewareName) {
                $middlewareService->activate($middlewareName);
            }
        }

        if ($plugin->getActive() === false) {
            $services = $instance->getServices();
            Oforge()->Services()->register($services);
            $instance->activate();
            $plugin->setActive(true);
            $this->entityManager()->update($plugin);
        }
        $instance->load();
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
        if (sizeof($dependants) > 0) {
            // (╯°□°）╯︵ ┻━┻
            throw new CouldNotDeactivatePluginException($pluginName, $dependants);
        }

        if (sizeof($pluginToDeactivateInstance->getMiddlewares()) > 0) {
            /** @var MiddlewareService $middlewareService */
            $middlewareService = Oforge()->Services()->get('middleware');
            $middlewareNames   = [];
            foreach ($pluginToDeactivateInstance->getMiddlewares() as $middleware) {
                $middlewareNames = array_merge($middlewareNames, $this->getMiddlewareNames($middleware));
            }

            foreach ($middlewareNames as $middlewareName) {
                $middlewareService->deactivate($middlewareName);
            }
        }

        if ($pluginToDeactivate->getActive() === true) {
            if (isset($pluginToDeactivateInstance)) {
                $pluginToDeactivateInstance->deactivate();
            }

            $pluginToDeactivate->setActive(false);
            $this->entityManager()->update($pluginToDeactivate);
        }
    }

    /**
     * @param $middleware
     *
     * @return array
     */
    private function getMiddlewareNames($middleware) {
        $middlewareNames = [];

        if (array_key_exists('class', $middleware)) {
            array_push($middlewareNames, $middleware['class']);
        } elseif (is_array($middleware)) {
            foreach ($middleware as $key => $value) {
                if (is_array($value) && isset($value['class'])) {
                    array_push($middlewareNames, $value['class']);
                }
            }
        }

        return $middlewareNames;
    }

}
