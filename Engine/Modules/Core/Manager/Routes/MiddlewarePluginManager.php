<?php
/**
 * Created by PhpStorm.
 * User: Alexander Wegner
 * Date: 13.11.2018
 * Time: 11:12
 */

namespace Oforge\Engine\Modules\Core\Manager\Routes;

use Oforge\Engine\Modules\Core\Models\Plugin\Middleware;

class MiddlewarePluginManager {
    /**
     * @var Middleware[]
     */
    private $activeMiddlewares;
    
    /**
     * MiddlewarePluginManager constructor.
     *
     * @param Middleware[] $activeMiddlewares
     */
    public function __construct( $activeMiddlewares ) {
        $this->activeMiddlewares = $activeMiddlewares;
    }
    
    /**
     * Add append and prepend middleware to Slim
     *
     * @param  \Psr\Http\Message\ServerRequestInterface $request PSR7 request
     * @param  \Psr\Http\Message\ResponseInterface $response PSR7 response
     * @param  callable $next Next middleware
     *
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function __invoke( $request, $response, $next ) {
        foreach ( $this->activeMiddlewares as $middleware ) {
            $className = $middleware->getClass();
            
            if ( method_exists( $className, "prepend" ) ) {
                $newresponse = (new $className())->prepend( $request, $response );
                if(isset($newresponse)) $response = $newresponse;
            }
        }
        
        $response = $next( $request, $response );
        
        foreach ( $this->activeMiddlewares as $middleware ) {
            $className = $middleware->getClass();
            
            if ( method_exists( $className, "append" ) ) {
                $newresponse = (new $className())->append( $request, $response );
                if(isset($newresponse)) $response = $newresponse;
            }
        }
        return $response;
    }
}
