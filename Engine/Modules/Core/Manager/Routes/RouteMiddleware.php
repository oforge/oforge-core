<?php

namespace Oforge\Engine\Modules\Core\Manager\Routes;

use Oforge\Engine\Modules\Core\Models\Routes\Route;

class RouteMiddleware
{
    protected $route = null;

    public function __construct(Route $route)
    {
        $this->route = $route;
    }
    
    /**
     * Example middleware invokable class
     *
     * @param  \Psr\Http\Message\ServerRequestInterface $request PSR7 request
     * @param  \Psr\Http\Message\ResponseInterface $response PSR7 response
     * @param  callable $next Next middleware
     *
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function __invoke($request, $response, $next)
    {
        Oforge()->View()->assign(
            [
                "language" => $this->route->getLanguageId(),
                "controller_method" => $this->route->getController()
            ]
        );

        return $next($request, $response);
    }
}