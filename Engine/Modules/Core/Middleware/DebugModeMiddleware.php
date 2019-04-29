<?php

namespace Oforge\Engine\Modules\Core\Middleware;

use Oforge\Engine\Modules\Core\Services\ConfigService;

/**
 * Class DebugModeMiddleware
 *
 * @package Oforge\Engine\Modules\Core\Middleware
 */
class DebugModeMiddleware {

    /** @inheritDoc */
    public function __invoke($request, $response, $next) {
        /** @var ConfigService $configService */
        $configService = Oforge()->Services()->get('config');
        $debugMode     = $configService->get('system_debug');

        if ($debugMode) {
            Oforge()->View()->assign(['debug_mode' => true]);
        }

        return $next($request, $response);
    }

}
