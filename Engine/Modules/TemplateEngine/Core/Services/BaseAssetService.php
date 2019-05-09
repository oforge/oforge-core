<?php
/**
 * Created by PhpStorm.
 * User: Matthaeus.Schmedding
 * Date: 07.11.2018
 * Time: 10:39
 */

namespace Oforge\Engine\Modules\TemplateEngine\Core\Services;

use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use Oforge\Engine\Modules\Core\Exceptions\ServiceNotFoundException;
use Oforge\Engine\Modules\Core\Exceptions\TemplateNotFoundException;
use Oforge\Engine\Modules\Core\Helper\Statics;
use Oforge\Engine\Modules\Core\Helper\StringHelper;
use Oforge\Engine\Modules\Core\Models\Plugin\Plugin;
use Oforge\Engine\Modules\Core\Services\KeyValueStoreService;
use Oforge\Engine\Modules\Core\Services\PluginAccessService;

class BaseAssetService {
    protected $key = "";
    /**
     * @var $store KeyValueStoreService
     */
    protected $store;

    /**
     * BaseAssetService constructor.
     *
     * @throws ServiceNotFoundException
     */
    public function __construct() {
        $this->store = Oforge()->Services()->get("store.keyvalue");
    }

    /**
     * Create assets like JavaScript or CSS Files
     *
     * @param string $scope
     * @param string $context
     *
     * @return string
     */
    public function build(string $context, string $scope = TemplateAssetService::DEFAULT_SCOPE) : string {
        // check if the /var/public folder exists. if not, create it.
        if (!file_exists(ROOT_PATH . Statics::ASSET_CACHE_DIR)) {
            mkdir(ROOT_PATH . Statics::ASSET_CACHE_DIR, 0750, true);
        }

        return "";
    }

    /**
     * @param string $scope
     *
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function clear(string $scope = TemplateAssetService::DEFAULT_SCOPE) {
        $this->store->set($this->getAccessKey($scope), "");
    }

    /**
     * @param string $scope
     *
     * @return string
     */
    public function getUrl(string $scope = TemplateAssetService::DEFAULT_SCOPE) : string {
        $value = $this->store->get($this->getAccessKey($scope));
        if (isset($value)) {
            return $value;
        }

        return $this->build($scope);
    }

    /**
     * @param string $scope
     *
     * @return bool
     */
    public function isBuild(string $scope = TemplateAssetService::DEFAULT_SCOPE) {
        $value = $this->store->get($this->getAccessKey($scope));

        return isset($value);
    }

    /**
     * @param string $scope
     *
     * @return string
     */
    protected function getAccessKey(string $scope = TemplateAssetService::DEFAULT_SCOPE) : string {
        return "compiled." . $this->key . ".url." . $scope;
    }

    /**
     * @return array
     * @throws ORMException
     * @throws OptimisticLockException
     * @throws ServiceNotFoundException
     * @throws TemplateNotFoundException
     */
    protected function getAssetsDirectories() {
        /** @var TemplateManagementService $templateManagementService */
        $templateManagementService = Oforge()->Services()->get("template.management");
        $activeTemplate            = $templateManagementService->getActiveTemplate();

        $paths = [ROOT_PATH . DIRECTORY_SEPARATOR . Statics::TEMPLATE_DIR . DIRECTORY_SEPARATOR . Statics::DEFAULT_THEME];

        /** @var $pluginAccessService PluginAccessService */
        $pluginAccessService = Oforge()->Services()->get("plugin.access");

        /** @var $plugins Plugin[] */
        $plugins = $pluginAccessService->getActive();

        foreach ($plugins as $plugin) {
            $viewsDir = ROOT_PATH . DIRECTORY_SEPARATOR . Statics::PLUGIN_DIR . DIRECTORY_SEPARATOR . $plugin->getName() . DIRECTORY_SEPARATOR
                        . Statics::VIEW_DIR;

            if (file_exists($viewsDir)) {
                array_push($paths, $viewsDir);
            }
        }

        $templatePath = ROOT_PATH . DIRECTORY_SEPARATOR . Statics::TEMPLATE_DIR . DIRECTORY_SEPARATOR . $activeTemplate->getName();

        if (!in_array($templatePath, $paths)) {
            array_push($paths, $templatePath);
        }

        return $paths;
    }

    /**
     * remove generated asset files that aren't used anymore.
     * - Scan the cache asset directory
     * - find all files based on file extension except the currently used file
     * - delete
     *
     * @param string $newFileName the currently used file
     */
    protected function removeOldAssets(string $folder, string $newFileName, string $extension) {
        $files = scandir($folder);
        foreach ($files as $file) {
            if (StringHelper::endsWith($file, $extension) && strpos($file, $newFileName) === false) {
                unlink($folder . DIRECTORY_SEPARATOR . $file);
            }
        }
    }
}
