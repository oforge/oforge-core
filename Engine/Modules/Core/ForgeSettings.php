<?php
namespace Oforge\Engine\Modules\Core;

use Monolog\Logger;
use Oforge\Engine\Modules\Core\Models\ForgeDataBase;

/**
 * Class ForgeSettings
 * Loads all Settings that need to come from the filesystem, e.g. database configuration.
 *
 * @package Oforge\Engine\Modules\Core
 */
class ForgeSettings {
    protected static $instance = null;
    private $settings = [];

    public static function getInstance($test = false) {
        if (null === self::$instance) {
            self::$instance = new ForgeSettings($test ? (ROOT_PATH . '/test.config.php') : (ROOT_PATH . '/config.php'));
        }
        return self::$instance;
    }

    protected function __construct($path = null) {
        if(isset($path)) $this->path = $path;
        else $this->path = ROOT_PATH . '/config.php';
    }
    
    /**
     * Load the settings
     */
    public function load() {
        $config = require_once $this->path;
        $this->settings = $config;
    }
    
    /**
     * get a specific setting value based on a defined key
     *
     * @param string $key
     *
     * @return array|mixed
     */
    public function get(string $key)  {
        return array_key_exists($key, $this->settings) ? $this->settings[$key] : array();
    }
}
