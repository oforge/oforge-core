<?php
/**
 * Created by PhpStorm.
 * User: Matthaeus.Schmedding
 * Date: 07.11.2018
 * Time: 10:39
 */

namespace Oforge\Engine\Modules\TemplateEngine\Core\Middleware;

use Oforge\Engine\Modules\Core\Exceptions\ServiceNotFoundException;
use Slim\Http\Request;
use Slim\Http\Response;

/**
 * Class AssetsMiddleware
 *
 * @package Oforge\Engine\Modules\TemplateEngine\Core\Middleware
 */
class AssetsMiddleware {

    /**
     * Middleware call before the controller call
     *
     * @param Request $request
     * @param Response $response
     *
     * @throws ServiceNotFoundException
     */
    public function prepend($request, $response) {
        $assetScope = Oforge()->View()->get('meta')['route']['assetScope'];

        $data = [
            'assets' => [
                'js'  => Oforge()->Services()->get('assets.js')->getUrl($assetScope),
                'css' => Oforge()->Services()->get('assets.css')->getUrl($assetScope),
            ],
        ];

        Oforge()->View()->assign($data);
    }

}
