<?php

namespace Oforge\Engine\Modules\Core\Helper;

use Oforge\Engine\Modules\Core\Abstracts\AbstractBootstrap;
use Oforge\Engine\Modules\Core\Exceptions\InvalidClassException;
use RecursiveCallbackFilterIterator;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;
use SplFileInfo;

/**
 * Class Helper
 *
 * @package Oforge\Engine\Modules\Core\Helper
 * @deprecated
 */
class Helper {
    public const OMIT = ['.', '..', '.git', 'var', 'vendor', 'node_modules'];

    /**
     * get all Bootstrap.php files inside a defined path
     *
     * @param string $path
     *
     * @return array
     */
    public static function getBootstrapFiles(string $path) {
        $result = [];

        $cacheFile = ROOT_PATH . Statics::CACHE_DIR . DIRECTORY_SEPARATOR . basename($path) . ".cache";

        if (file_exists($cacheFile) && Oforge()->Settings()->get("mode") != "development") {
            $result = unserialize(file_get_contents($cacheFile));
        } else {
            $result = self::findFiles($path, "bootstrap.php");
            file_put_contents($cacheFile, serialize($result));
        }

        return $result;
    }

    /**
     * get all template files inside a defined path
     *
     * @param string $path
     *
     * @return array
     */
    public static function getTemplateFiles(string $path) {
        $cacheFile = ROOT_PATH . Statics::CACHE_DIR . DIRECTORY_SEPARATOR . basename($path) . ".cache";

        if (file_exists($cacheFile) && Oforge()->Settings()->get("mode") != "development") {
            $result = unserialize(file_get_contents($cacheFile));
        } else {
            $result = self::findFiles($path, "template.php");
            file_put_contents($cacheFile, serialize($result));
        }

        return $result;
    }

    /**
     * get the instance of a module/plugin based on the bootstrap file
     *
     * @param $pluginName
     *
     * @return mixed
     * @throws InvalidClassException
     */
    public static function getBootstrapInstance($pluginName) : AbstractBootstrap {
        $className = $pluginName . "\\Bootstrap";

        if (is_subclass_of($className, AbstractBootstrap::class)) {
            return new $className();
        }
        throw new InvalidClassException($className, AbstractBootstrap::class);
    }

    /**
     * Search recursive for for files with name inside a path.
     *
     * @param string $path string Directory or file path.
     * @param string $searchFileName Search file name.
     *
     * @return string[] Array with full path to files.
     */
    public static function findFiles(string $path, string $searchFileName) {
        $omits  = array_fill_keys(self::OMIT, true);
        $path   = realpath($path);
        $result = [];

        $recursiveDirectoryIterator = new RecursiveDirectoryIterator($path, RecursiveDirectoryIterator::FOLLOW_SYMLINKS);
        $recursiveFilterIterator    = new RecursiveCallbackFilterIterator($recursiveDirectoryIterator,
            function ($file, $key, $iterator) use ($omits, $searchFileName) {
                /** @var SplFileInfo $file */
                return !isset($omits[$file->getFileName()]);
            });
        $recursiveIteratorIterator  = new RecursiveIteratorIterator($recursiveFilterIterator);
        foreach ($recursiveIteratorIterator as $file) {
            if (strtolower($file->getFileName()) === $searchFileName) {
                $classpath = str_replace($path . DIRECTORY_SEPARATOR, "", $file->getPath());
                $classpath = str_replace(".php", "", $classpath);

                $result[$classpath] = $file->getPath() . DIRECTORY_SEPARATOR . $file->getFileName();
            }
        }

        return $result;
    }

    /**
     * Get some metadata from a file.
     * If the file is a psr4 conform class, the function finds the namespace and classname and returns them as an array.
     * Otherwise null.
     *
     * @param string $fileName
     *
     * @return array|null
     */
    public static function getFileMeta(string $fileName) {
        $file      = fopen($fileName, 'r');
        $class     = '';
        $namespace = '';
        $buffer    = '';
        $i         = 0;
        $fileMeta  = [];

        while (!$class) {
            if (feof($file)) {
                break;
            }

            $buffer .= fread($file, 512);
            $tokens = token_get_all($buffer);

            if (strpos($buffer, '{') === false) {
                continue;
            }

            for (; $i < count($tokens); $i++) {
                if ($tokens[$i][0] === T_NAMESPACE) {
                    for ($j = $i + 1; $j < count($tokens); $j++) {
                        if ($tokens[$j][0] === T_STRING) {
                            $namespace .= '\\' . $tokens[$j][1];
                        } elseif ($tokens[$j] === '{' || $tokens[$j] === ';') {
                            break;
                        }
                    }
                }

                if ($tokens[$i][0] === T_CLASS) {
                    for ($j = $i + 1; $j < count($tokens); $j++) {
                        if ($tokens[$j] === '{') {
                            $class = $tokens[$i + 2][1];
                        }
                    }
                }
            }
        }

        if (StringHelper::startsWith($namespace, "\\")) {
            $namespace = ltrim($namespace, "\\");
        }

        if (strlen($class) > 0) {
            $fileMeta['class_name'] = $class;
        }

        if (strlen($namespace) > 0) {
            $fileMeta['namespace'] = $namespace;
        }

        if (sizeof($fileMeta) < 2) {
            return null;
        }

        return $fileMeta;
    }

}
