<?php
/**
 * Created by PhpStorm.
 * User: Matthaeus.Schmedding
 * Date: 07.11.2018
 * Time: 10:39
 */

namespace Oforge\Engine\Modules\TemplateEngine\Core\Services;

use Oforge\Engine\Modules\Core\Exceptions\ServiceNotFoundException;

class TemplateAssetService {
    public const DEFAULT_SCOPE = "Frontend";

    /**
     * @param string $scope
     * @param string $context
     *
     * @throws ServiceNotFoundException
     */
    public function build($context, $scope = self::DEFAULT_SCOPE) {
        Oforge()->Services()->get("assets.css")->build($context, $scope);
        Oforge()->Services()->get("assets.js")->build($context, $scope);
        Oforge()->Services()->get("assets.static")->build($context, $scope);
    }

    /**
     * @throws ServiceNotFoundException
     */
    public function clear() {
        Oforge()->Services()->get("assets.css")->clear();
        Oforge()->Services()->get("assets.js")->clear();
    }
}
