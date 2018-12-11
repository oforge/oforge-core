<?php
/**
 * Created by PhpStorm.
 * User: Matthaeus.Schmedding
 * Date: 07.11.2018
 * Time: 10:39
 */

namespace Oforge\Engine\Modules\TemplateEngine\Services;

class TemplateAssetService
{
    public const DEFAULT_SCOPE = "Frontend";

    /**
     * @param string $scope
     */
    public function build($scope = self::DEFAULT_SCOPE)
    {
        Oforge()->Services()->get("assets.css")->build($scope);
        Oforge()->Services()->get("assets.js")->build($scope);
        Oforge()->Services()->get("assets.static")->build($scope);
    }
    
    /**
     * @throws \Oforge\Engine\Modules\Core\Exceptions\ServiceNotFoundException
     */
    public function clear()
    {
        Oforge()->Services()->get("assets.css")->clear();
        Oforge()->Services()->get("assets.js")->clear();
    }
}