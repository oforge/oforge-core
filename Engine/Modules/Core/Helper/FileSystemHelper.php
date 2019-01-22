<?php

namespace Oforge\Engine\Modules\Core\Helper;

/**
 * FileSystemHelper
 *
 * @package Oforge\Engine\Modules\Core\Helper
 */
class FileSystemHelper {
    public const OMIT        = ['.', '..'];
    public const OMIT_OFORGE = ['.', '..', 'var', 'vendor'];

    /**
     * Prevent instance.
     */
    private function __construct() {
    }

    /**
     * Deletes single file or directory.
     * Directories can optionally be recursively deleted and empty directories will not be deleted.
     *
     * @param string $path Path to file or directory
     * @param bool $recursive Delete directory recursive?
     * @param bool $deleteEmptyDirs Delete empty directories?
     *
     * @return bool
     */
    public static function delete(string $path, bool $recursive = false, bool $deleteEmptyDirs = true) : bool {
        if (empty($path)) {
            return false;
        }
        $path = realpath($path);
        if (is_dir($path)) {
            $filenames = array_diff(scandir($path), ['.', '..']);
            $success   = true;
            foreach ($filenames as $filename) {
                if ($recursive) {
                    $success &= self::delete($path . DIRECTORY_SEPARATOR . $filename, $recursive, $deleteEmptyDirs);
                }
            }
            if ($deleteEmptyDirs) {
                $tmp = @rmdir($path);
                if (!$tmp) {
                    Oforge()->Logger()->get()->warning('Could not delete directory: ' . $path);
                }
                $success &= $tmp;
            }

            return $success;
        }
        if (@unlink($path)) {
            return true;
        }
        Oforge()->Logger()->get()->warning('Could not delete file: ' . $path);

        return false;
    }

    /**
     * Get all Bootstrap.php files inside a defined path
     *
     * @param string $path
     *
     * @return string[]
     */
    public static function getBootstrapFiles(string $path) {
        return self::findFiles($path, 'bootstrap');
    }

    /**
     * Get all template files inside a defined path
     *
     * @param string $path
     *
     * @return string[]
     */
    public static function findThemeBootstrapFiles(string $path) {
        return self::findFiles($path, 'theme');
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
        $result = [];
        if (is_dir($path)) {
            $fileNames = scandir($path);
            foreach ($fileNames as $fileName) {
                if (in_array($fileName, self::OMIT_OFORGE)) {
                    continue;
                }
                $filePath = $path . DIRECTORY_SEPARATOR . $fileName;
                $result   = array_merge($result, self::findFiles($filePath, $searchFileName));
            }
        } else {
            $fileName = pathinfo($path, PATHINFO_FILENAME);
            if (!in_array($fileName, self::OMIT_OFORGE) && strtolower($fileName) === strtolower($searchFileName)) {
                $result[] = $path;
            }
        }

        return $result;
    }

    /**
     * Get all sub directories recursive based on the defined path, except the folders to oforge omit.
     *
     * @param string $path
     *
     * @return string[]
     */
    public static function getSubDirectories(string $path) {
        $result = [];
        if (!is_dir($path)) {
            return $result;
        }
        $fileNames = scandir($path);

        foreach ($fileNames as $fileName) {
            $filePath = $path . DIRECTORY_SEPARATOR . $fileName;

            if (is_dir($filePath) && !in_array($fileName, self::OMIT_OFORGE)) {
                $result[] = $filePath;
                $result   = array_merge($result, self::getSubDirectories($filePath));
            }
        }

        return $result;
    }

}
