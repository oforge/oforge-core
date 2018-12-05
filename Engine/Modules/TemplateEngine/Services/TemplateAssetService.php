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
    /**
     * @throws \Oforge\Engine\Modules\Core\Exceptions\ServiceNotFoundException
     */
    public function build()
    {
        Oforge()->Services()->get("assets.css")->build();
        Oforge()->Services()->get("assets.js")->build();
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