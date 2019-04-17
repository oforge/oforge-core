<?php

namespace Oforge\Engine\Modules\Core\Middleware;

use Oforge\Engine\Modules\Core\Exceptions\ConfigElementNotFoundException;
use Oforge\Engine\Modules\Core\Exceptions\ServiceNotFoundException;
use Oforge\Engine\Modules\Core\Services\ConfigService;
use Oforge\Engine\Modules\Core\Services\Session\SessionManagementService;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class SessionMiddleware {
    /**
     * @param  ServerRequestInterface $request PSR7 request
     * @param  ResponseInterface $response PSR7 response
     * @param  callable $next Next middleware
     *
     * @return mixed
     * @throws ConfigElementNotFoundException
     * @throws ServiceNotFoundException
     */
    public function __invoke( $request, $response, $next ) {
        /** @var SessionManagementService $sessionManager */
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
