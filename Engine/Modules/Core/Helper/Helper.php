<?php

namespace Oforge\Engine\Modules\Core\Helper;

use Oforge\Engine\Modules\Core\Abstracts\AbstractBootstrap;
use Oforge\Engine\Modules\Core\Exceptions\InvalidClassException;

class Helper {
    protected static $omitFolders = [".", "..", "vendor", "var"];
    
    /**
     * Get all directories recursive based on the defined path, except the folders to omit
     * @param string $path
     *
     * @return array
     */
    public static function getAllDirs(string $path) {
        $result = [];
        $tmp = scandir($path);

        foreach($tmp as $dir) {
            $v = $path . DIRECTORY_SEPARATOR   . $dir;

            if(is_dir($v) && !in_array($dir, self::$omitFolders)) {
                array_push($result, $v);
                $result = array_merge($result, Helper::getAllDirs($v));
            }
        }
        
        return $result;
    }
    
    /**
     * get all Bootstrap.php files inside a defined path
     *
     * @param string $path
     *
     * @return array
     */
    public static function getBootstrapFiles(string $path) {
        return self::findFilesInPath($path, "bootstrap.php");
    }
    
    /**
     * get all template files inside a defined path
     *
     * @param string $path
     *
     * @return array
     */
    public static function getTemplateFiles(string $path) {
        return self::findFilesInPath($path, "template.php");
    }
    
    /**
     * search for filename inside a path
     *
     * @param $path string
     * @param $fileName string
     *
     * @return array
     */
    private static function findFilesInPath($path, $fileName) {
        $result = [];
        if(is_dir($path)) {
            $tmp = scandir($path);
        
            foreach($tmp as $dir) {
                $v = $path . DIRECTORY_SEPARATOR   . $dir;
            
                if(is_dir($v) && !in_array($dir, self::$omitFolders)) {
                    $result = array_merge($result, Helper::findFilesInPath($v, $fileName));
                } else if(strtolower($dir) == $fileName) {
                    $result[basename($path)] = $v;
                }
            }
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
        
        if ( is_subclass_of( $className, AbstractBootstrap::class ) ) {
            return new $className();
        }
        throw new InvalidClassException($className, AbstractBootstrap::class);
    }
}
