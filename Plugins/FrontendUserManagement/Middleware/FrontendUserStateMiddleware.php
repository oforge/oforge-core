<?php

namespace FrontendUserManagement\Middleware;

use FrontendUserManagement\Services\FrontendUserService;
use Oforge\Engine\Modules\Core\Exceptions\ServiceNotFoundException;
use Slim\Http\Request;
use Slim\Http\Response;

class FrontendUserStateMiddleware {

    public function prepend(Request $request, Response $response) {
        try {
            /** @var FrontendUserService $userService */
            $userService = Oforge()->Services()->get("frontend.user");
            if ($userService->isLoggedIn()) {
                Oforge()->View()->assign(['user_logged_in' => true]);
            }
            if (!Oforge()->View()->has('current_user')) {
                $user = $userService->getUser();
                if ($user != null) {
                    Oforge()->View()->assign(['current_user' => $user->toArray(1, ['password'])]);
                }
            }
        } catch (ServiceNotFoundException $exception) {
        }
    }

}
