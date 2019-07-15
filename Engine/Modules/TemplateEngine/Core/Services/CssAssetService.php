<?php

namespace Oforge\Engine\Modules\TemplateEngine\Core\Services;

use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use Leafo\ScssPhp\Compiler;
use MatthiasMullie\Minify\CSS;
use Oforge\Engine\Modules\Core\Exceptions\ServiceNotFoundException;
use Oforge\Engine\Modules\Core\Exceptions\Template\TemplateNotFoundException;
use Oforge\Engine\Modules\Core\Helper\Statics;
use Oforge\Engine\Modules\TemplateEngine\Core\Models\ScssVariable;

class CssAssetService extends BaseAssetService {
    /**
     * CssAssetService constructor.
     *
     * @throws ServiceNotFoundException
     */
    public function __construct() {
        parent::__construct();
        $this->key = "css";
    }

    /**
     * Compiles .scss files in the following order:
     * 1. Base Theme
     * 2. Plugins
     * 3. Active Theme (unless it's the Base Theme)
     * @param string $context
     * @param string $scope
     *
     * @return string
     * @throws ORMException
     * @throws OptimisticLockException
     * @throws ServiceNotFoundException
     * @throws TemplateNotFoundException
     * @throws \Oforge\Engine\Modules\Core\Exceptions\ConfigElementNotFoundException
     */
    public function build(string $context, string $scope = TemplateAssetService::DEFAULT_SCOPE) : string {
        parent::build($context);

        $dirs = $this->getAssetsDirectories();

        $fileName = "style." . bin2hex(openssl_random_pseudo_bytes(16));
        $folder     = Statics::ASSET_CACHE_DIR . DIRECTORY_SEPARATOR . $scope . DIRECTORY_SEPARATOR . $this->key;
        $fullFolder = ROOT_PATH . $folder;
        $output     = $folder . DIRECTORY_SEPARATOR . $fileName;
        $outputFull = ROOT_PATH . $output;

        if (!file_exists($fullFolder) || (file_exists($fullFolder) && !is_dir($fullFolder))) {
            mkdir($fullFolder, 0750, true);
        }

        // get scss variables and add to compiler
        $scss          = new Compiler();
        $scssService   = Oforge()->Services()->get('scss.variables');
        $dbVariables   = $scssService->get(Statics::TEMPLATE_DIR . '\\' . $context . '\\Template', $scope);
        $scssVariables = Statics::DEFAULT_SCSS_VARIABLES;

        /** @var ScssVariable $var */
        foreach ($dbVariables as $var) {
            $scssVariables[$var->getName()] = $var->getValue();
        }
        $scss->setVariables($scssVariables);


        // check if source mapping is active
        /** @var  $configService */
        $configService = Oforge()->Services()->get('config');
        $sourceMap = $configService->get('css_source_map');

        if($sourceMap) {
            // source map setup
            $scss->setSourceMap(Compiler::SOURCE_MAP_INLINE);
            $scss->setSourceMapOptions(array(
                'sourceMapBasepath' => ROOT_PATH,
                'sourceRoot'        => '/',
            ));
        }

        // iterate over all plugins, current theme and base theme
        $importPaths = [];
        foreach ($dirs as $dir) {
            $scssFolder = $dir . DIRECTORY_SEPARATOR . $scope . DIRECTORY_SEPARATOR . Statics::ASSETS_DIR . DIRECTORY_SEPARATOR . Statics::ASSETS_SCSS
                          . DIRECTORY_SEPARATOR;
            if (file_exists($scssFolder) && file_exists($scssFolder . Statics::ASSETS_ALL_SCSS)) {
                $importPaths[] = $scssFolder;
            }
        }
        // build global import file to reference all .scss files
        $global = "";
        foreach ($importPaths as $importPath) {
            $global .= '@import "' . $importPath . 'all' . '";' . "\n";
        }
        file_put_contents($fullFolder . DIRECTORY_SEPARATOR . Statics::ASSETS_ALL_SCSS, $global);

        // compile css from global import file
        $scss->addImportPath($fullFolder);
        $result = $scss->compile('@import "' . Statics::ASSETS_ALL_SCSS . '";');
        $this->removeOldAssets($fullFolder, $fileName, ".css");

        file_put_contents($outputFull . ".css", $result);

        // only minify if source mapping is not active
        if(!$sourceMap) {
            $minifier = new CSS($outputFull . ".css");
            $minifier->minify($outputFull . ".min.css");
            $this->store->set($this->getAccessKey($scope), $output . ".min.css");
            return $output . ".min.css";
        }

        $this->store->set($this->getAccessKey($scope), $output . ".css");
        return $output . ".css";
    }
}
