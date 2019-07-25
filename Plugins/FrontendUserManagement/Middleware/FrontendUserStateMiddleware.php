<?php

namespace FrontendUserManagement\Middleware;

use FrontendUserManagement\Services\FrontendUserService;
use Slim\Http\Request;
use Slim\Http\Response;

class FrontendUserStateMiddleware {

    public function prepend(Request $request, Response $response) {
        /** @var FrontendUserService $userService */
        $userService = Oforge()->Services()->get("frontend.user");
        if ($userService->isLoggedIn()) {
            Oforge()->View()->assign(['user_logged_in' => true]);
        }
    }

}
