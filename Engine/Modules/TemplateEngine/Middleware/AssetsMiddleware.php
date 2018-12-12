<?php
/**
 * Created by PhpStorm.
 * User: Matthaeus.Schmedding
 * Date: 07.11.2018
 * Time: 10:39
 */

namespace Oforge\Engine\Modules\TemplateEngine\Middleware;

use Slim\Http\Request;
use Slim\Http\Response;

class AssetsMiddleware
{
    /**
     * Middleware call before the controller call
     *
     * @param Request $request
     * @param Response $response
     *
     * @return ?Response
     * @throws \Oforge\Engine\Modules\Core\Exceptions\ServiceNotFoundException
     */
    public function prepend($request, $response)
    {
        $meta = Oforge()->View()->get("meta");

        $data = [
            "assets" =>
                [
                    "js" => Oforge()->Services()->get("assets.js")->getUrl($meta["asset_scope"]),
                    "css" => Oforge()->Services()->get("assets.css")->getUrl($meta["asset_scope"])
                ]
        ];

        Oforge()->View()->assign($data);
    }
}