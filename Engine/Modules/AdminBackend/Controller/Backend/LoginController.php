<?php

namespace Oforge\Engine\Modules\AdminBackend\Controller\Backend;

use Oforge\Engine\Modules\Auth\Services\BackendLoginService;
use \Oforge\Engine\Modules\Core\Abstracts\AbstractController;
use Slim\Http\Request;
use Slim\Http\Response;
use Slim\Router;

class LoginController extends AbstractController
{
    /**
     * @param Request $request
     * @param Response $response
     *
     * @throws \Exception
     */
    public function indexAction(Request $request, Response $response) {}

    /**
     * Login Action
     *
     * @param Request $request
     * @param Response $response
     *
     * @return Response
     * @throws \Oforge\Engine\Modules\Core\Exceptions\ServiceNotFoundException
     */
    public function processAction(Request $request, Response $response)
    {
        if (empty($_SESSION)) {
            print_r("No session :/");
            die();
        }
        
        /**
         * @var $backendLoginService BackendLoginService
         */
        $backendLoginService = Oforge()->Services()->get('backend.login');

        /**
         * @var $router Router
         */
        $router = Oforge()->App()->getContainer()->get("router");
        $uri = $router->pathFor("backend_login");
        $body = $request->getParsedBody();
        $jwt = null;
    
        /**
         * no token was sent
         */
        if (empty($_POST['token'])) {
            Oforge()->Logger()->get()->addWarning("Someone tried to do a backend login with a form without csrf token! Redirecting to backend login.");
            return $response->withRedirect($uri, 302);
        }
    
        /**
         * invalid token was sent
         */
        if (!hash_equals($_SESSION['token'], $_POST['token'])) {
            Oforge()->Logger()->get()->addWarning("Someone tried a backend login without a valid form csrf token! Redirecting back to login.");
            return $response->withRedirect($uri, 302);
        }
        
        /**
         * no email or password body was sent
         */
        if (!array_key_exists("email", $body) ||
            !array_key_exists("password", $body)) {
            return $response->withRedirect($router->pathFor("backend_login"), 302);
        }
    
        $jwt = $backendLoginService->login($body["email"], $body["password"]);
    
        /**
         * $jwt is null if the login credentials are incorrect
         */
        if (!isset($jwt)) {
            return $response->withRedirect($uri, 302);
        }
    
        $_SESSION['auth'] = $jwt;
        
        $uri = $router->pathFor("backend_dashboard");
        
        return $response->withRedirect($uri, 302);
    }
}
