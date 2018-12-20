<?php
namespace Oforge\Engine\Modules\Core\Abstracts;

/**
 * Class AbstractBootstrap
 * Specific Bootstrap classes are needed to either autoload Modules or Plugins
 * @package Oforge\Engine\Modules\Core\Abstracts
 */
abstract class AbstractBootstrap
{
    /**
     * @var array
     *
     */
    protected $models = [];
    protected $endpoints = [];
    protected $services = [];
    protected $middleware = [];
    protected $cronJobs = [];
    protected $dependencies = [];

    protected $order = 1337;

    /**
     * @return array
     */
    public function getEndpoints(): array
    {
        return $this->endpoints;
    }
    
    /**
     * @return array
     */
    public function getServices(): array
    {
        return $this->services;
    }
    
    /**
     * @return array
     */
    public function getModels(): array
    {
        return $this->models;
    }
    
    /**
     * @return array
     */
    public function getMiddleware(): array
    {
        return $this->middleware;
    }
    
    /**
     * @return array
     */
    public function getCronJobs(): array
    {
        return $this->cronJobs;
    }
    
    /**
     * @return int
     */
    public function getOrder(): int
    {
        return $this->order;
    }

    /**
     * @return array
     */
    public function getDependencies(): array
    {
        return $this->dependencies;
    }

    public function install() {}
    public function update() {}
    public function uninstall() {}
    public function activate() {}
    public function deactivate() {}
    
    public function load() {}
}
