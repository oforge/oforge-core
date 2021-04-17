<?php

namespace Oforge\Engine\Modules\Core\Middleware;

use Oforge\Engine\Modules\Core\Exceptions\ConfigElementNotFoundException;
use Oforge\Engine\Modules\Core\Exceptions\ServiceNotFoundException;
use Oforge\Engine\Modules\Core\Manager\SessionManager;
use Oforge\Engine\Modules\Core\Services\ConfigService;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

/**
 * Class SessionMiddleware
 *
 * @package Oforge\Engine\Modules\Core\Middleware
 */
class SessionMiddleware {

    /**
     * @param ServerRequestInterface $request PSR7 request
     * @param ResponseInterface $response PSR7 response
     * @param callable $next Next middleware
     *
     * @return mixed
     * @throws ConfigElementNotFoundException
     * @throws ServiceNotFoundException
     */
    public function __invoke($request, $response, $next) {
        SessionManager::start();
        /** @var ConfigService $configService */
        $configService = Oforge()->Services()->get('config');
        $debugMode     = $configService->get('debug_mode');

        if ($debugMode) {
            /** for debugging purposes */
            $debugSession  = $configService->get('debug_session');
            if ($debugSession) {
                Oforge()->View()->assign(['debug.session' => $_SESSION]);
            }
        }

        return $next($request, $response);
    }

}
