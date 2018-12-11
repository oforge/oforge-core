<?php

use Oforge\Engine\Modules\Core\App;
use Oforge\Engine\Modules\Core\ForgeSettings;

use Oforge\Engine\Modules\Core\Manager\Modules\ModuleManager;
use Oforge\Engine\Modules\Core\Manager\Routes\RouteManager;
use Oforge\Engine\Modules\Core\Manager\Logger\LogManager;
use Oforge\Engine\Modules\Core\Manager\Plugins\PluginManager;
use Oforge\Engine\Modules\Core\Manager\Services\ServiceManager;
use Oforge\Engine\Modules\Core\Models\ForgeDataBase;
// TODO: find a better way to use a TemplateEngine Module
use Oforge\Engine\Modules\Core\Abstracts\AbstractTemplateManager;
use Oforge\Engine\Modules\Core\Abstracts\AbstractViewManager;

class BlackSmith
{
    /**
     * The main instance to start the whole application.
     *
     * @var BlackSmith
     */
    protected static $instance = null;

    /**
     * App
     *
     * @var App
     */
    private $app = null;

    /**
     * Container
     *
     * @var \Slim\Container
     */
    private $container = null;

    /**
     *  DataBase
     *
     * @var ForgeDataBase
     */
    private $db = null;

    /**
     *  RouteManager
     *
     * @var RouteManager
     */
    private $router = null;

    /**
     * LogManager
     *
     * @var LogManager
     */
    private $logger = null;

    /**
     * ForgeSettings
     *
     * @var ForgeSettings
     */
    private $settings = null;

    /**
     * Services
     *
     * @var ServiceManager
     */
    private $services = null;

    /**
     * PluginManager
     *
     * @var PluginManager
     */
    private $pluginManager = null;

    /**
     * TemplateManager
     *
     * @var AbstractTemplateManager
     */
    private $templateManager = null;

    /**
     * ViewManager
     *
     * @var AbstractViewManager
     */
    private $viewManager = null;
    
    /**
     * Create a singleton instance of the inner core
     * @return BlackSmith
     */
    public static function getInstance(): BlackSmith
    {
        if (!isset(self::$instance)) {
            self::$instance = new BlackSmith();
        }

        return self::$instance;
    }
    
    /**
     * BlackSmith constructor.
     */
    protected function __construct()
    {
        Oforge($this);
    }
    
    public function App(): App
    {
        if (!isset($this->app)) throw new \RuntimeException('Oforge fire does not burn. Ask the blacksmith to start forging.');
        return $this->app;
    }

    public function DB(): ForgeDataBase
    {
        if (!isset($this->db)) throw new \RuntimeException('Oforge fire does not burn. Ask the blacksmith to start forging.');

        return $this->db;
    }

    public function Router(): RouteManager
    {
        if (!isset($this->router)) throw new \RuntimeException('Oforge fire does not burn. Ask the blacksmith to start forging.');
        return $this->router;
    }

    public function Container(): \Slim\Container
    {
        if (!isset($this->container)) throw new \RuntimeException('Oforge fire does not burn. Ask the blacksmith to start forging.');
        return $this->container;
    }

    public function Logger(): LogManager
    {
        if (!isset($this->logger)) throw new \RuntimeException('Oforge fire does not burn. Ask the blacksmith to start forging.');
        return $this->logger;
    }

    public function Settings(): ForgeSettings
    {
        if (!isset($this->settings)) throw new \RuntimeException('Oforge fire does not burn. Ask the blacksmith to start forging.');
        return $this->settings;
    }

    public function Services(): ServiceManager
    {
        if (!isset($this->services)) throw new \RuntimeException('Oforge fire does not burn. Ask the blacksmith to start forging.');
        return $this->services;
    }

    public function Plugins(): PluginManager
    {
        if (!isset($this->pluginManager)) throw new RuntimeException('Oforge fire does not burn. Ask the blacksmith to start forging.');
        return $this->pluginManager;
    }

    public function Templates(): AbstractTemplateManager
    {
        if (!isset($this->templateManager)) throw new RuntimeException('Oforge fire does not burn. Ask the blacksmith to start forging.');
        return $this->templateManager;
    }

    public function View(): AbstractViewManager
    {
        if (!isset($this->viewManager)) throw new RuntimeException('Oforge fire does not burn. Ask the blacksmith to start forging.');
        return $this->viewManager;
    }


    /**
     * @param bool $start definies if slim should be started or not
     * @param bool $test definies if test environment should be used
     *
     * @throws Exception
     * @throws \Oforge\Engine\Modules\Core\Exceptions\ServiceNotFoundException
     * @throws \Slim\Exception\MethodNotAllowedException
     * @throws \Slim\Exception\NotFoundException
     */
    public function forge($start = true, $test = false)
    {
        /**
        * Load settings from disk like mysql credentials, log paths, s.o.
        */
        $this->settings = ForgeSettings::getInstance($test);
        $this->settings->load();

        /**
         * Init logger
         */
        $this->logger = new LogManager($this->settings->get("logger"));

        $this->logger->get()->info("first " . microtime(true));
        /**
         * Connect to database
         */
        $this->db = ForgeDataBase::getInstance();
        $this->db->init($this->settings->get("db"));


        $this->logger->get()->info("db " . microtime(true));

        /**
         * Start service manager
         */
        $this->services = ServiceManager::getInstance();


        $this->logger->get()->info("service " . microtime(true));
        /*
        * Start slim application
        */
        $this->app = App::getInstance();
        $this->container = $this->App()->getContainer();

        $this->logger->get()->info("slim " . microtime(true));

        /*
        * Init and load modules
        */
        $modules = ModuleManager::getInstance();
        $modules->init();

        $this->logger->get()->info("modules " . microtime(true));

        /*
        * Init and load plugins
        */
        $this->pluginManager = PluginManager::getInstance();
        $this->pluginManager->init();


        $this->logger->get()->info("plugin " . microtime(true));

        /*
         * Init route manager
         */
        $this->router = RouteManager::getInstance();
        $this->router->init();

        $this->logger->get()->info("routes " . microtime(true));

        if($this->templateManager) {
            $this->templateManager->init();
        }

        $this->logger->get()->info("template " . microtime(true));
        /*
         * Let the Blacksmith forge all the things \°/
         */
        if ($start) {
            $this->app->run();
        }

        $this->logger->get()->info("slim start " . microtime(true));
    }

    /**
     * @param AbstractViewManager $viewManager
     */
    public function setViewManager(AbstractViewManager $viewManager)
    {
        $this->viewManager = $viewManager;
    }

    /**
     * @param AbstractTemplateManager $templateManager
     */
    public function setTemplateManager(AbstractTemplateManager $templateManager)
    {
        $this->templateManager = $templateManager;
    }
}

/**
 * Returns application instance.
 * This Function call is globally available.
 * So you can call Oforge()->{App(), Service(), DB(), ...}->whatever
 *
 * @param BlackSmith $newInstance
 *
 * @return BlackSmith
 */
function Oforge(BlackSmith &$newInstance = null): BlackSmith
{
    static $instance;

    if (isset($newInstance)) {
        $oldInstance = $instance;
        $instance = $newInstance;

        return $newInstance;
    } elseif (!isset($instance)) {
        throw new RuntimeException('Oforge fire does not burn. Ask the blacksmith to start forging.');
    }

    return $instance;
}
