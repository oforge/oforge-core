<?php
namespace FrontendUserManagement\Middleware;

use Oforge\Engine\Modules\Auth\Services\AuthService;
use Oforge\Engine\Modules\Auth\Services\Permissions;
use Slim\Http\Request;
use Slim\Http\Response;
use Slim\Router;

class FrontendUserStateMiddleware {
    public function prepend(Request $request, Response $response) {
        if (isset($_SESSION['user_logged_in']) && $_SESSION['user_logged_in'] === true) {
            Oforge()->View()->assign(['user_logged_in' => true]);
        }
    }
}