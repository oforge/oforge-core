<?php
namespace FrontendUserManagement\Middleware;

use Slim\Http\Request;
use Slim\Http\Response;

class FrontendUserStateMiddleware {
    public function prepend(Request $request, Response $response) {
        if (isset($_SESSION['user_logged_in']) && $_SESSION['user_logged_in'] === true) {
            Oforge()->View()->assign(['user_logged_in' => true]);
        }
    }
}
