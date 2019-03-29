<?php

namespace Oforge\Engine\Modules\Core\Middleware;

use Oforge\Engine\Modules\Core\Services\ConfigService;

class DebugModeMiddleware {
    public function __invoke( $request, $response, $next ) {
        /**
         * @var $configService ConfigService
         */
        $configService = Oforge()->Services()->get( 'config' );
        $debugMode = $configService->get('system_debug');

        if($debugMode) {
            Oforge()->View()->assign(['debug_mode' => true]);
        }
        return $next($request, $response);
    }
}