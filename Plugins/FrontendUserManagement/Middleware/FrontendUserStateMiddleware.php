<?php

namespace FrontendUserManagement\Middleware;

use FrontendUserManagement\Services\FrontendUserService;
use Slim\Http\Request;
use Slim\Http\Response;

class FrontendUserStateMiddleware {
    public function prepend(Request $request, Response $response) {
        if (isset($_SESSION['user_logged_in']) && $_SESSION['user_logged_in'] === true) {
            Oforge()->View()->assign(['user_logged_in' => true]);
        }

        /**
         * @var $userService FrontendUserService
         */
        $userService = Oforge()->Services()->get("frontend.user");

        $user = $userService->getUser();

        if ($user != null) {
            Oforge()->View()->assign(['current_user' => $user->toArray(1, ["password"])]);
        }
    }
}
