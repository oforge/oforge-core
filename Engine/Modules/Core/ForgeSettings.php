<?php

namespace Oforge\Engine\Modules\Core;

/**
 * Class ForgeSettings
 * Loads all Settings that need to come from the filesystem, e.g. database configuration.
 *
 * @package Oforge\Engine\Modules\Core
 */
class ForgeSettings {
    private const CONFIG_FILE      = ROOT_PATH . '/config.php';
    private const CONFIG_FILE_TEST = ROOT_PATH . '/test.config.php';
    /** @var ForgeSettings $instance */
    protected static $instance = null;
    /** @var array $settings */
    private $settings = [];

    /**
     * ForgeSettings constructor.
     *
     * @param string|null $path
     */
    protected function __construct(?string $path = null) {
        $this->path = isset($path) ? $path : self::CONFIG_FILE;
    }

    /**
     * @param bool $test
     *
     * @return ForgeSettings
     */
    public static function getInstance($test = false) : ForgeSettings {
        if (null === self::$instance) {
            self::$instance = new ForgeSettings($test ? self::CONFIG_FILE_TEST : self::CONFIG_FILE);
        }

        return self::$instance;
    }

    /**
     * Load the settings
     */
    public function load() {
        $config         = require_once $this->path;
        $this->settings = $config;
    }

    /**
     * get a specific setting value based on a defined key
     *
     * @param string $key
     * @param mixed $default
     *
     * @return array|mixed
     */
    public function get(string $key, $default = []) {
        return isset($this->settings[$key]) ? $this->settings[$key] : $default;
    }

    /**
     * Compare value with expected value.
     *
     * @param string $key
     * @param mixed $expectedValue Expected value to compare.
     * @param mixed $default Default value if value not set.
     *
     * @return bool
     */
    public function is(string $key, $expectedValue, $default = []) : bool {
        return $this->get($key, $default) === $expectedValue;
    }

    /**
     * @return bool
     */
    public function isDevelopmentMode() : bool {
        return $this->is('mode', 'development');
    }

    /**
     * @return bool
     */
    public function isProductionMode() : bool {
        return $this->is('mode', 'production');
    }

}
