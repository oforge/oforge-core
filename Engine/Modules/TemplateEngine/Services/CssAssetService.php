<?php

namespace Oforge\Engine\Modules\TemplateEngine\Services;

use Leafo\ScssPhp\Compiler;
use MatthiasMullie\Minify\CSS;
use Oforge\Engine\Modules\Core\Helper\Statics;
use Oforge\Engine\Modules\TemplateEngine\Models\ScssVariable;

class CssAssetService extends BaseAssetService {
    /**
     * CssAssetService constructor.
     *
     * @throws \Oforge\Engine\Modules\Core\Exceptions\ServiceNotFoundException
     */
    public function __construct() {
        parent::__construct();
        $this->key = "css";
    }

    /**
     * @param string $scope
     * @param $context
     *
     * @return string
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     * @throws \Oforge\Engine\Modules\Core\Exceptions\ServiceNotFoundException
     * @throws \Oforge\Engine\Modules\Core\Exceptions\TemplateNotFoundException
     */
    public function build(string $context, string $scope = TemplateAssetService::DEFAULT_SCOPE) : string {
        parent::build($context);

        $dirs = $this->getAssetsDirectories();

        $fileName = "style." . bin2hex(openssl_random_pseudo_bytes(16));

        $folder     = Statics::ASSET_CACHE_DIR . DIRECTORY_SEPARATOR . $scope . DIRECTORY_SEPARATOR . $this->key;
        $fullFolder = ROOT_PATH . $folder;
        $output     = $folder . DIRECTORY_SEPARATOR . $fileName;
        $outputFull = ROOT_PATH . $output;

        $result = "";

        if (!file_exists($fullFolder) || (file_exists($fullFolder) && !is_dir($fullFolder))) {
            mkdir($fullFolder, 0750, true);
        }

        // get scss variables and add to compiler
        $scss          = new Compiler();
        $scssService   = Oforge()->Services()->get('scss.variables');
        $dbVariables   = $scssService->get(Statics::TEMPLATE_DIR . '\\' . $context . '\\Template', $scope);
        $scssVariables = [];

        /** @var ScssVariable $var */
        foreach ($dbVariables as $var) {
            $scssVariables[$var->getName()] = $var->getValue();
        }

        $scss->setVariables($scssVariables);

        //iterate over all plugins, current theme and base theme
        foreach ($dirs as $dir) {
            $folder = $dir . DIRECTORY_SEPARATOR . $scope . DIRECTORY_SEPARATOR . Statics::ASSETS_DIR . DIRECTORY_SEPARATOR . Statics::ASSETS_SCSS
                      . DIRECTORY_SEPARATOR;
            if (file_exists($folder) && file_exists($folder . Statics::ASSETS_ALL_SCSS)) {
                $scss->setImportPaths($folder);
                $result .= $scss->compile('@import "' . Statics::ASSETS_ALL_SCSS . '";');
            }
        }

        file_put_contents($outputFull . ".css", $result);

        $minifier = new CSS($outputFull . ".css");
        $minifier->minify($outputFull . ".min.css");

        $this->store->set($this->getAccessKey($scope), $output . ".min.css");
        $this->removeOldAssets($fullFolder, $fileName, ".css");

        return $output . ".min.css";
    }
}
