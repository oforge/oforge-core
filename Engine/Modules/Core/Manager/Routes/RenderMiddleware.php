<?php

namespace Oforge\Engine\Modules\Core\Manager\Routes;

class RenderMiddleware {
    /**
     * Add a "Fetch Controller Data" Middleware function
     *
     * @param  \Psr\Http\Message\ServerRequestInterface $request PSR7 request
     * @param  \Psr\Http\Message\ResponseInterface $response PSR7 response
     * @param  callable $next Next middleware
     *
     * @return \Psr\Http\Message\ResponseInterface
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     * @throws \Oforge\Engine\Modules\Core\Exceptions\ServiceNotFoundException
     * @throws \Twig_Error_Loader
     */
    public function __invoke( $request, $response, $next ) {
        $response = $next( $request, $response );
        $data = Oforge()->View()->fetch();
        return Oforge()->Templates()->render( $request, $response, $data );
    }
}
