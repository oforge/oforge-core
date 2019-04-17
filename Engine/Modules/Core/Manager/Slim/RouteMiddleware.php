<?php

namespace Oforge\Engine\Modules\Core\Manager\Slim;

use Oforge\Engine\Modules\Core\Models\Endpoint\Endpoint;

class RouteMiddleware {
    protected $endpoint = null;

    public function __construct(Endpoint $endpoint) {
        $this->endpoint = $endpoint;
    }

    /**
     * Example middleware invokable class
     *
     * @param \Psr\Http\Message\ServerRequestInterface $request PSR7 request
     * @param \Psr\Http\Message\ResponseInterface $response PSR7 response
     * @param callable $next Next middleware
     *
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function __invoke($request, $response, $next) {
        Oforge()->View()->assign([
            'meta' => [
                'route'       => $this->endpoint->toArray(),
                // 'language'          => $this->endpoint->getLanguageID(),
                'controller'  => [
                    'class'  => $this->endpoint->getControllerClass(),
                    'method' => $this->endpoint->getControllerMethod(),
                ],
                'asset_scope' => $this->endpoint->getAssetScope(),
                'order'       => $this->endpoint->getOrder(),
            ],
        ]);

        return $next($request, $response);
    }
}
