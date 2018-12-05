<?php

namespace Oforge\Engine\Modules\Core\Manager\Routes;

use Oforge\Engine\Modules\Core\Helper\StringHelper;
use Oforge\Engine\Modules\Core\Models\Plugin\Middleware;
use Oforge\Engine\Modules\Core\Models\Routes\Route;
use Oforge\Engine\Modules\Core\Services\MiddlewareService;

class RouteManager {
    protected static $instance = null;

    public static function getInstance() {
        if ( ! isset( self::$instance ) ) {
            self::$instance = new RouteManager();
        }
        return self::$instance;
    }
    
    /**
     * Make all routes, that come from the database, work
     *
     * @throws \Oforge\Engine\Modules\Core\Exceptions\ServiceNotFoundException
     */
    public function init() {
        $em   = Oforge()->DB()->getManager();
        $repo = $em->getRepository( Route::class );
        
        $activeRoutes = $repo->findBy( array( 'active' => 1 ) );
        $container    = Oforge()->App()->getContainer();
        
        foreach ( $activeRoutes as $route ) {
            /**
             * @var $route Route
             */
            $className = StringHelper::substringBefore( $route->getController(), ":" );
            
            if ( !$container->has( $className ) ) {
                $container[ $className ] = function ( $container ) use ( $className ) {
                    return new $className;
                };
            }
            
            $call = Oforge()->App()
                            ->any( $route->getPath(), $route->getController() )
                            ->setName( $route->getName() );

          /**
             * @var $middlewares MiddlewareService
             */
            $middlewares = Oforge()->Services()->get( "middleware" );
            /**
             * @var $activeMiddlewares Middleware[]
             */
            $activeMiddlewares = $middlewares->getActive( $route->getName() );

            $call->add( new MiddlewarePluginManager($activeMiddlewares) );
            $call->add( new RenderMiddleware() );
            $call->add( new RouteMiddleware( $route ) );
        }
    }
}
