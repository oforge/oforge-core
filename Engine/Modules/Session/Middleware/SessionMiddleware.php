<?php

namespace Oforge\Engine\Modules\Session\Middleware;

use Oforge\Engine\Modules\Core\Services\ConfigService;
use Oforge\Engine\Modules\Session\Services\SessionManagementService;

class SessionMiddleware {
    /**
     * @param  \Psr\Http\Message\ServerRequestInterface $request PSR7 request
     * @param  \Psr\Http\Message\ResponseInterface $response PSR7 response
     * @param  callable $next Next middleware
     *
     * @return mixed
     * @throws \Oforge\Engine\Modules\Core\Exceptions\ConfigElementNotFoundException
     * @throws \Oforge\Engine\Modules\Core\Exceptions\ServiceNotFoundException
     */
    public function __invoke( $request, $response, $next ) {
        /**
         * @var $sessionManager SessionManagementService
         */
        $sessionManager = Oforge()->Services()->get("session.management");
        $sessionManager->sessionStart();

        /** @var ConfigService $configService */
        $configService = Oforge()->Services()->get('config');
        $sessionDebug = $configService->get('session_debug');

        /** for debugging purposes */
        if ($sessionDebug) {
            Oforge()->View()->assign(["session" => $_SESSION]);
        }

        return $next($request, $response);
    }
}
