<?php
namespace Test\Middleware;

class HomeMiddleware {
    
    /**
     * Middleware call before the controller call
     *
     * @param  \Psr\Http\Message\ServerRequestInterface $request PSR7 request
     * @param  \Psr\Http\Message\ResponseInterface $response PSR7 response
     *
     * @return void
     */
    public function prepend( $request, $response) {
        $data = ["greeting" => "Ist jetzt prepend"];
        Oforge()->View()->assign($data);
    }
    
    /**
     * Middleware call after the controller call
     *
     * @param  \Psr\Http\Message\ServerRequestInterface $request PSR7 request
     * @param  \Psr\Http\Message\ResponseInterface $response PSR7 response
     *
     * @return void
     */
    public function append( $request, $response) {
        $data = ["greeting" => "ist jetzt append"];
        Oforge()->View()->assign($data);
    }
}
