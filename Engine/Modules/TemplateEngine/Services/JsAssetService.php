<?php
/**
 * Created by PhpStorm.
 * User: Matthaeus.Schmedding
 * Date: 07.11.2018
 * Time: 10:39
 */

namespace Oforge\Engine\Modules\TemplateEngine\Services;

use MatthiasMullie\Minify\JS;
use Oforge\Engine\Modules\Core\Helper\Statics;

class JsAssetService extends BaseAssetService
{
    /**
     * JsAssetService constructor.
     * @throws \Oforge\Engine\Modules\Core\Exceptions\ServiceNotFoundException
     */
    public function __construct()
    {
        parent::__construct();
        $this->key = "js";
    }
    
    /**
     * @param string $scope
     *
     * @return string
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     * @throws \Oforge\Engine\Modules\Core\Exceptions\ServiceNotFoundException
     */
    public function build(string $scope = TemplateAssetService::DEFAULT_SCOPE): string
    {
        parent::build();
        $dirs = $this->getAssetsDirectories();
        $hasFilesToMinify = false;
        
        $fileName = "scripts." . bin2hex(openssl_random_pseudo_bytes(16));

        $folder = Statics::ASSET_CACHE_DIR . DIRECTORY_SEPARATOR . $scope . DIRECTORY_SEPARATOR . $this->key;
        $fullFolder = ROOT_PATH . $folder;
        $output = $folder . DIRECTORY_SEPARATOR . $fileName;
        $outputFull = ROOT_PATH . $output;

        if(!file_exists($fullFolder) || (file_exists($fullFolder) && !is_dir($fullFolder))) {
            mkdir($fullFolder, 0750, true);
        }

        //iterate over all plugins, current theme and base theme
        foreach ($dirs as $dir) {
            $folder = $dir . DIRECTORY_SEPARATOR . $scope . DIRECTORY_SEPARATOR . Statics::ASSETS_DIR  . DIRECTORY_SEPARATOR . Statics::ASSETS_JS . DIRECTORY_SEPARATOR;
            if (file_exists($folder) && file_exists($folder . Statics::ASSETS_IMPORT_JS)) {
                if ($file = fopen($folder . Statics::ASSETS_IMPORT_JS, "r")) {
                    while(!feof($file)) {
                        $line = trim(fgets($file));

                        if(strlen($line) > 0 && file_exists($folder . $line) ) {
                            file_put_contents($outputFull . ".js", file_get_contents($folder . $line), FILE_APPEND);
                            $hasFilesToMinify = true;
                        }
                    }
                    fclose($file);
                }
            }
        }

        if ($hasFilesToMinify) {
            $minifier = new JS($outputFull . ".js");
            $minifier->minify($outputFull . ".min.js");
    
            $this->store->set($this->getAccessKey($scope), $output . ".min.js");
            $this->removeOldAssets($fullFolder, $fileName ,".js");
    
            return $output . ".min.js";
        }
        return "";
    }
}
