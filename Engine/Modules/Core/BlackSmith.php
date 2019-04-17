<?php

use Oforge\Engine\Modules\Core\Abstracts\AbstractTemplateManager;
use Oforge\Engine\Modules\Core\Abstracts\AbstractViewManager;
use Oforge\Engine\Modules\Core\Forge\ForgeSettings;
use Oforge\Engine\Modules\Core\Forge\ForgeSlimApp;
use Oforge\Engine\Modules\Core\Manager\Logger\LoggerManager;
use Oforge\Engine\Modules\Core\Manager\Modules\ModuleManager;
use Oforge\Engine\Modules\Core\Manager\Plugins\PluginManager;
use Oforge\Engine\Modules\Core\Manager\Slim\SlimRouteManager;
use Oforge\Engine\Modules\Core\Manager\Services\ServiceManager;
use Oforge\Engine\Modules\Core\Forge\ForgeDatabase;

// TODO: find a better way to use a TemplateEngine Module

/**
 * Class BlackSmith
 */
class BlackSmith {
    /**
     * The main instance to start the whole application.
     *
     * @var BlackSmith
     */
    protected static $instance = null;
    /**
     * App
     *
     * @var ForgeSlimApp $forgeSlimApp
     */
    private $forgeSlimApp = null;
    /**
     * Container
     *
     * @var \Slim\Container $container
     */
    private $container = null;
    /**
     *  DataBase
     *
     * @var ForgeDatabase $db
     */
    private $db = null;
    /**
     *  RouteManager
     *
     * @var SlimRouteManager $slimRouteManagager
     */
    private $slimRouteManagager = null;
    /**
     * LogManager
     *
     * @var LoggerManager $logger
     */
    private $logger = null;
    /**
     * ForgeSettings
     *
     * @var ForgeSettings $settings
     */
    private $settings = null;
    /**
     * Services
     *
     * @var ServiceManager $services
     */
    private $services = null;
    /**
     * PluginManager
     *
     * @var PluginManager $pluginManager
     */
    private $pluginManager = null;
    /**
     * TemplateManager
     *
     * @var AbstractTemplateManager $templateManager
     */
    private $templateManager = null;
    /**
     * ViewManager
     *
     * @var AbstractViewManager $viewManager
     */
    private $viewManager = null;
    
    /**
     * Create a singleton instance of the inner core
     *
     * @return BlackSmith
     */
    public static function getInstance(): BlackSmith {
        if ( ! isset( self::$instance ) ) {
            self::$instance = new BlackSmith();
        }
        
        return self::$instance;
    }
    
    /**
     * BlackSmith constructor.
     */
    protected function __construct() {
        Oforge( $this );
    }
    
    public function App(): ForgeSlimApp {
        if ( ! isset( $this->forgeSlimApp ) ) {
            throw new \RuntimeException( 'Oforge fire does not burn. Ask the blacksmith to start forging.' );
        }
        
        return $this->forgeSlimApp;
    }
    
    public function DB(): ForgeDatabase {
        if ( ! isset( $this->db ) ) {
            throw new \RuntimeException( 'Oforge fire does not burn. Ask the blacksmith to start forging.' );
        }
        
        return $this->db;
    }
    
    public function SlimRouteManager(): SlimRouteManager {
        if ( ! isset( $this->slimRouteManagager ) ) {
            throw new \RuntimeException( 'Oforge fire does not burn. Ask the blacksmith to start forging.' );
        }
        
        return $this->slimRouteManagager;
    }
    
    public function Container(): \Slim\Container {
        if ( ! isset( $this->container ) ) {
            throw new \RuntimeException( 'Oforge fire does not burn. Ask the blacksmith to start forging.' );
        }
        
        return $this->container;
    }
    
    public function Logger(): LoggerManager {
        if ( ! isset( $this->logger ) ) {
            throw new \RuntimeException( 'Oforge fire does not burn. Ask the blacksmith to start forging.' );
        }
        
        return $this->logger;
    }
    
    public function Settings(): ForgeSettings {
        if ( ! isset( $this->settings ) ) {
            throw new \RuntimeException( 'Oforge fire does not burn. Ask the blacksmith to start forging.' );
        }
        
        return $this->settings;
    }
    
    public function Services(): ServiceManager {
        if ( ! isset( $this->services ) ) {
            throw new \RuntimeException( 'Oforge fire does not burn. Ask the blacksmith to start forging.' );
        }
        
        return $this->services;
    }
    
    public function Plugins(): PluginManager {
        if ( ! isset( $this->pluginManager ) ) {
            throw new RuntimeException( 'Oforge fire does not burn. Ask the blacksmith to start forging.' );
        }
        
        return $this->pluginManager;
    }
    
    public function Templates(): AbstractTemplateManager {
        if ( ! isset( $this->templateManager ) ) {
            throw new RuntimeException( 'Oforge fire does not burn. Ask the blacksmith to start forging.' );
        }
        
        return $this->templateManager;
    }
    
    public function View(): AbstractViewManager {
        if ( ! isset( $this->viewManager ) ) {
            throw new RuntimeException( 'Oforge fire does not burn. Ask the blacksmith to start forging.' );
        }
        
        return $this->viewManager;
    }
    
    
    /**
     * @param bool $start defines if slim should be started or not
     * @param bool $test defines if test environment should be used
     *
     * @throws \Oforge\Engine\Modules\Core\Exceptions\ServiceNotFoundException
     * @throws \Slim\Exception\MethodNotAllowedException
     * @throws \Slim\Exception\NotFoundException
     * @throws Exception
     */
    public function forge( $start = true, $test = false ) {
        /**
         * Load settings from disk like mysql credentials, log paths, s.o.
         */
        $startTime = microtime(true) * 1000;
        $this->settings = ForgeSettings::getInstance( $test );
        $this->settings->load();

        /**
         * Init logger
         */
        $this->logger = new LoggerManager( $this->settings->get( "logger" ) );


        /**
         * Connect to database
         */
        $this->db = ForgeDatabase::getInstance();
        $this->db->init( $this->settings->get( "db" ) );

        /**
         * Start service manager
         */
        $this->services = ServiceManager::getInstance();

        /*
        * Start slim application
        */
        $this->forgeSlimApp = ForgeSlimApp::getInstance();
        $this->container    = $this->App()->getContainer();

        $this->Logger()->get()->addInfo("Step 4 " . (         microtime(true)* 1000- $startTime));
        /*
        * Init and load modules
        */
        $modules = ModuleManager::getInstance();
        $modules->init();

        $this->Logger()->get()->addInfo("Step 5 " . (         microtime(true)* 1000- $startTime));
        /*
        * Init and load plugins
        */
        $this->pluginManager = PluginManager::getInstance();
        $this->pluginManager->init();

        $this->Logger()->get()->addInfo("Step 6 " . (         microtime(true)* 1000- $startTime));
        /*
         * Init route manager
         */
        $this->slimRouteManagager = SlimRouteManager::getInstance();
        $this->slimRouteManagager->init();

        $this->Logger()->get()->addInfo("Step 7 " . (   microtime(true)* 1000 - $startTime));
        /*
         * Let the Blacksmith forge all the things \°/
         */
        if ( $start ) {
            $this->forgeSlimApp->run();
        }
    }
    
    /**
     * @param AbstractViewManager $viewManager
     */
    public function setViewManager( AbstractViewManager $viewManager ) {
        $this->viewManager = $viewManager;
    }
    
    /**
     * @param AbstractTemplateManager $templateManager
     */
    public function setTemplateManager( AbstractTemplateManager $templateManager ) {
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
function Oforge( BlackSmith &$newInstance = null ): BlackSmith {
    static $instance;
    
    if ( isset( $newInstance ) ) {
        $oldInstance = $instance;
        $instance    = $newInstance;
        
        return $newInstance;
    } elseif ( ! isset( $instance ) ) {
        throw new RuntimeException( 'Oforge fire does not burn. Ask the blacksmith to start forging.' );
    }
    
    return $instance;
}
