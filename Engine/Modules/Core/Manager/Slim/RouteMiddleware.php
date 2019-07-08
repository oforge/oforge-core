<?php

namespace Oforge\Engine\Modules\Core\Manager\Slim;

use Oforge\Engine\Modules\Core\Models\Endpoint\Endpoint;
use Slim\Http\Request;
use Slim\Http\Response;

/**
 * Class RouteMiddleware
 *
 * @package Oforge\Engine\Modules\Core\Manager\Slim
 */
class RouteMiddleware {
    /** @var Endpoint */
    protected $endpoint = null;

    /**
     * RouteMiddleware constructor.
     *
     * @param Endpoint $endpoint
     */
    public function __construct(Endpoint $endpoint) {
        $this->endpoint = $endpoint;
    }

    /** @inheritDoc */
    public function __invoke(Request $request, Response $response, $next) {
        $routeInfo = $request->getAttribute('routeInfo');
        Oforge()->View()->assign([
            'meta' => [
                'route' => array_merge($this->endpoint->toArray(), [
                    'baseUrl' => $request->getUri()->getBaseUrl(),
                    'params'  => $routeInfo[2],
                    'query'   => $request->getQueryParams(),
                ]),
            ],
        ]);

        return $next($request, $response);
    }

}
