<?php

namespace Oforge\Engine\Modules\Core\Manager\Routes;

use Oforge\Engine\Modules\Core\Helper\ArrayHelper;
use Psr\Http\Message\ResponseInterface;
use Slim\Http\Request;
use Slim\Http\Response;

/**
 * Class RenderMiddleware
 *
 * @package Oforge\Engine\Modules\Core\Manager\Routes
 */
class RenderMiddleware {

    /**
     * Add a "Fetch Controller Data" Middleware function
     *
     * @param Request $request PSR7 request
     * @param Response $response PSR7 response
     * @param callable $next Next middleware
     *
     * @return ResponseInterface
     */
    public function __invoke($request, $response, $next) {
        $data = [];

        $twigFlash = Oforge()->View()->Flash();
        if ($twigFlash->hasMessages()) {
            $data['flashMessages'] = $twigFlash->getMessages();
            $twigFlash->clearMessages();
        }
        $response = $next($request, $response);
        if (empty($data)) {
            $data = Oforge()->View()->fetch();
        } else {
            $fetchedData = Oforge()->View()->fetch();

            $data = ArrayHelper::mergeRecursive($data, $fetchedData);
        }

        return Oforge()->Templates()->render($request, $response, $data);
    }

}
