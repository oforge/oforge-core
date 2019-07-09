<?php

namespace Oforge\Engine\Modules\Core\Manager\Cache;

use Exception;
use InvalidArgumentException;
use Monolog\Handler\RotatingFileHandler;
use Monolog\Handler\StreamHandler;
use Monolog\Logger;
use Oforge\Engine\Modules\Core\Exceptions\LoggerAlreadyExistException;
use Oforge\Engine\Modules\Core\Helper\ArrayHelper;
use Oforge\Engine\Modules\Core\Helper\FileSystemHelper;
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
     * @param $slot
     */
    public function cleanUp($slot) {
        FileSystemHelper::delete(ROOT_PATH . Statics::FUNCTION_CACHE_DIR . DIRECTORY_SEPARATOR . $slot, true);
    }

    /**
     * Returns cache instance
     *
     * @return mixed
     */
    public function get(string $slot, string $className, string $functionName, $arguments) {
        if ($this->exists($slot, $className, $functionName, $arguments)) {
            $fileName = $this->getFileName($slot, $className, $functionName, $arguments);

            $output = null;
            $files  = glob($fileName . "*");

            foreach ($files as $file) {
                if (strpos($file, "##") === false) {
                    // file expires in the future
                    $output = file_get_contents($file);
                } else {
                    $split = explode("##", $file);
                    if (sizeof($split) == 2) {
                        $durationString = $split[1];
                        try {
                            $interval = new \DateInterval('P' . $durationString);
                            $date     = new \DateTime(date('c', filemtime($file)));

                            $expiresDate = $date->add($interval);
                            $now         = new \DateTime();

                            if ($expiresDate > $now) {
                                // file expires in the future
                                $output = file_get_contents($file);
                            }
                        } catch (Exception $exception) {
                            Oforge()->Logger()->get()->error($exception->getMessage(), $exception->getTrace());
                        }
                    }
                }
            }

            return $output != null ? unserialize($output) : null;
        }

        return null;
    }

    /**
     * Returns cache instance
     *
     * @param string $slot
     * @param string $className
     * @param string $functionName
     * @param $arguments
     *
     * @return mixed
     */
    public function exists(string $slot, string $className, string $functionName, $arguments) {
        $fileName = $this->getFileName($slot, $className, $functionName, $arguments);

        $files = glob($fileName . "*");

        foreach ($files as $file) {
            if (strpos($file, "##") === false) {
                return true;
            } else {
                $split = explode("##", $file);
                if (sizeof($split) == 2) {
                    $durationString = $split[1];
                    try {
                        $interval = new \DateInterval('P' . $durationString);
                        $date     = new \DateTime(date('c', filemtime($file)));

                        $expiresDate = $date->add($interval);
                        $now         = new \DateTime();

                        if ($expiresDate > $now) {
                            return true;
                        }
                    } catch (Exception $exception) {
                        Oforge()->Logger()->get()->error($exception->getMessage(), $exception->getTrace());
                    }
                }
            }
        }

        return false;
    }

    /**
     * Returns cache instance.
     */
    public function set(string $slot, string $className, string $functionName, $arguments, $result, $duration = 'T5M') {
        $dirName = $this->getDirName($slot, $className, $functionName);

        @mkdir($dirName, 0755, true);
        $fileName = $this->getFileName($slot, $className, $functionName, $arguments) . "##" . $duration;

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
