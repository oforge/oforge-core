<?php

namespace Oforge\Engine\Modules\Core\Manager\Slim;

use Oforge\Engine\Modules\Core\Models\Endpoint\Endpoint;
use Slim\Http\Request;
use Slim\Http\Response;
use Slim\Http\Uri;

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
                    'baseUrl' => $this->getUriBaseUrl($request),
                    'params'  => $routeInfo[2],
                    'query'   => $request->getQueryParams(),
                ]),
            ],
        ]);

        return $next($request, $response);
    }

    private function getUriBaseUrl(Request $request) : string {
        /** @var Uri $uri */
        $uri      = $request->getUri();
        $scheme   = $uri->getScheme();
        $host     = $uri->getHost();
        $port     = $uri->getPort();
        $basePath = $uri->getBasePath();

        $scheme   = ($scheme === '' ? '' : ($scheme . '://'));
        $port     = ($port === null ? '' : (':' . $port));
        $basePath = rtrim($basePath, '/');

        $baseUrl = $scheme . $host . $port . $basePath;

        return $baseUrl;
    }

}
