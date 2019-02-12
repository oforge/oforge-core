<?php
/**
 * Created by PhpStorm.
 * User: Alexander Wegner
 * Date: 11.02.2019
 * Time: 10:05
 */

namespace FrontendUserManagement\Controller\Frontend;

use FrontendUserManagement\Services\FrontendUserLoginService;
use Oforge\Engine\Modules\Core\Abstracts\AbstractController;
use Oforge\Engine\Modules\Session\Services\SessionManagementService;
use Slim\Http\Request;
use Slim\Http\Response;
use Slim\Router;

class LoginController extends AbstractController {
    public function indexAction(Request $request, Response $response) {
        
        $bla = print_r($_SESSION, true);

        Oforge()->View()->assign(["msg" => $bla]);
    }
    
    /**
     * @param Request $request
     * @param Response $response
     *
     * @return Response
     * @throws \Oforge\Engine\Modules\Core\Exceptions\ServiceNotFoundException
     */
    public function processAction(Request $request, Response $response) {
        if (empty($_SESSION)) {
            print_r("No session :/");
            die();
        }
    
        /** @var FrontendUserLoginService $loginService */
        $loginService = Oforge()->Services()->get('frontend.user.management.login');
    
        /**
         * @var $router Router
         */
        $router = Oforge()->App()->getContainer()->get("router");
        $uri = $router->pathFor("frontend_login");
    
        /**
         * disallow direct processAction call. Only post action is allowed
         */
        if (!$request->isPost()) {
            return $response->withRedirect($uri, 302);
        }
    
        $body = $request->getParsedBody();
        $jwt = null;
    
        /**
         * no token was sent
         */
        if (!isset($body['token']) || empty($body['token'])) {
            Oforge()->Logger()->get()->addWarning("Someone tried to do a backend login with a form without csrf token! Redirecting to backend login.");
        
            return $response->withRedirect($uri, 302);
        }
    
        /**
         * invalid token was sent
         */
        if (!hash_equals($_SESSION['token'], $body['token'])) {
            Oforge()->Logger()->get()->addWarning("Someone tried a backend login without a valid form csrf token! Redirecting back to login.");
            return $response->withRedirect($uri, 302);
        }
    
        /**
         * no email or password body was sent
         */
        if (!array_key_exists("frontend_login_email", $body) ||
            !array_key_exists("frontend_login_password", $body)) {
            return $response->withRedirect($router->pathFor("frontend_login"), 302);
        }
    
        $jwt = $loginService->login($body["frontend_login_email"], $body["frontend_login_password"]);
    
        /**
         * $jwt is null if the login credentials are incorrect
         */
        if (!isset($jwt)) {
            return $response->withRedirect($uri, 302);
        }
    
        /**
         * @var $sessionManagement SessionManagementService
         */
        $sessionManagement = Oforge()->Services()->get('session.management');
        $sessionManagement->regenerateSession();
    
        $_SESSION['auth'] = $jwt;
    
        $uri = $router->pathFor("frontend_profile");
    
        return $response->withRedirect($uri, 302);
    }
}