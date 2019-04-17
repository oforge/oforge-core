<?php

namespace Oforge\Engine\Modules\Core\Manager\Slim;

use Oforge\Engine\Modules\Core\Models\Endpoint\Endpoint;

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
    public function __invoke($request, $response, $next) {
        Oforge()->View()->assign([
            'meta' => [
                'route'       => $this->endpoint->toArray(),
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
