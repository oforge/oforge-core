<?php

namespace Oforge\Engine\Modules\Core\Manager\Cache;

use Exception;
use InvalidArgumentException;
use Monolog\Handler\RotatingFileHandler;
use Monolog\Handler\StreamHandler;
use Monolog\Logger;
use Oforge\Engine\Modules\Core\Exceptions\LoggerAlreadyExistException;
use Oforge\Engine\Modules\Core\Helper\ArrayHelper;
use Oforge\Engine\Modules\Core\Helper\Statics;

/**
 * Manager for cache.
 *
 * @package Oforge\Engine\Modules\Core\Manager\Cache
 */
class CacheManager {
    /**
     * @var CacheManager $instance
     */
    protected static $instance = null;

    public static function getInstance() {
        if (null === self::$instance) {
            self::$instance = new CacheManager();
        }

        return self::$instance;
    }

    /**
     * Deletes old cache files
     *
     * @param int $days
     */
    public function cleanUp($slot) {
    }

    /**
     * Returns cache instance
     *
     * @return mixed
     */
    public function get(string $slot, string $className, string $functionName, $arguments) {
        if ($this->exists($slot, $className, $functionName, $arguments)) {
            $fileName = $this->getFileName($slot, $className, $functionName, $arguments);

            return unserialize(file_get_contents($fileName));
        }

        return null;
    }

    /**
     * Returns cache instance
     *
     * @return mixed
     */
    public function exists(string $slot, string $className, string $functionName, $arguments) {
        $fileName = $this->getFileName($slot, $className, $functionName, $arguments);

        return file_exists($fileName);
    }

    /**
     * Returns cache instance.
     */
    public function set(string $slot, string $className, string $functionName, $arguments, $result) {
        $dirName = $this->getDirName($slot, $className, $functionName);
        @mkdir($dirName, 0777, true);

        $fileName = $this->getFileName($slot, $className, $functionName, $arguments);

        file_put_contents($fileName, serialize($result));
    }

    private function getDirName(string $slot, string $className, string $functionName) {
        $shortName = (new \ReflectionClass($className))->getShortName();

        return ROOT_PATH . Statics::FUNCTION_CACHE_DIR . DIRECTORY_SEPARATOR . $slot . DIRECTORY_SEPARATOR . $shortName . DIRECTORY_SEPARATOR . $functionName;

    }

    private function getFileName(string $slot, string $className, string $functionName, $arguments) {
        $hash = md5(serialize($arguments));

        return $this->getDirName($slot, $className, $functionName) . DIRECTORY_SEPARATOR . $hash;

    }

}
