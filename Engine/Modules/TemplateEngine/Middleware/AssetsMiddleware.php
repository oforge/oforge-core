<?php
/**
 * Created by PhpStorm.
 * User: Matthaeus.Schmedding
 * Date: 07.11.2018
 * Time: 10:39
 */

namespace Oforge\Engine\Modules\TemplateEngine\Middleware;

class AssetsMiddleware
{
    /**
     * Middleware call before the controller call
     *
     * @param  \Psr\Http\Message\ServerRequestInterface $request PSR7 request
     * @param  \Psr\Http\Message\ResponseInterface $response PSR7 response
     *
     * @return void
     * @throws \Oforge\Engine\Modules\Core\Exceptions\ServiceNotFoundException
     */
    public function prepend($request, $response)
    {
        $data = [
            "assets" =>
                [
                    "js" => Oforge()->Services()->get("assets.js")->getUrl(),
                    "css" => Oforge()->Services()->get("assets.css")->getUrl()
                ]
        ];

        Oforge()->View()->assign($data);
    }
}