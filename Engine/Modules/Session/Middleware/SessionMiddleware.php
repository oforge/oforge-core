<?php
/**
 * Created by PhpStorm.
 * User: Alexander Wegner
 * Date: 12.12.2018
 * Time: 13:53
 */

namespace Oforge\Engine\Modules\Session\Middleware;

use Oforge\Engine\Modules\Session\Services\SessionManagementService;
use Slim\Http\Request;
use Slim\Http\Response;

class SessionMiddleware {
    /**
     * @param Request $request
     * @param Response $response
     *
     * @throws \Oforge\Engine\Modules\Core\Exceptions\ServiceNotFoundException
     */
    public function prepend(Request $request, Response $response) {
        /**
         * @var $sessionManager SessionManagementService
         */
        $sessionManager = Oforge()->Services()->get("session.management");
        $sessionManager->sessionStart(180);
    }
}