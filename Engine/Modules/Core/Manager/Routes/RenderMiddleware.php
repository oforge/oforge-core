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
     */
    public function __invoke( $request, $response, $next ) {
        $data = [];

        if (isset($_SESSION['flashMessage'])) {
            $data['flashMessage'] = $_SESSION['flashMessage'];
            unset($_SESSION['flashMessage']);
        }
        $response = $next( $request, $response );
        if (empty($data)) {
            $data = Oforge()->View()->fetch();
        } else {
            $data = array_merge($data, Oforge()->View()->fetch());
        }
        return Oforge()->Templates()->render( $request, $response, $data );
    }
}
