<?php

namespace Oforge\Engine\Modules\Core\Manager\Slim;

use Oforge\Engine\Modules\Core\Helper\ArrayHelper;
use Psr\Http\Message\ResponseInterface;
use Slim\Http\Request;
use Slim\Http\Response;

/**
 * Class RenderMiddleware
 *
 * @package Oforge\Engine\Modules\Core\Manager\Slim
 */
class RenderMiddleware {

    /** @inheritDoc */
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
