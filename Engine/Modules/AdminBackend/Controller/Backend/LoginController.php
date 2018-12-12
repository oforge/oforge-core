<?php

namespace Oforge\Engine\Modules\AdminBackend\Controller\Backend;

use Oforge\Engine\Modules\Auth\Services\BackendLoginService;
use \Oforge\Engine\Modules\Core\Abstracts\AbstractController;
use Slim\Http\Request;
use Slim\Http\Response;

class LoginController extends AbstractController
{
    /**
     * @param Request $request
     * @param Response $response
     *
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
        /**
         * @var $backendLoginService BackendLoginService
         */
        $backendLoginService = Oforge()->Services()->get('backend.login');

        $body = $request->getParsedBody();
        $jwt = null;
        
        if (array_key_exists("email", $body) &&
            array_key_exists("password", $body)) {
            $jwt = $backendLoginService->login($body["email"], $body["password"]);
        }

        /**
         * @var $router Router
         */
        $router = Oforge()->App()->getContainer()->get("router");

        if (isset($jwt)) {
            $cookie = "authorization=" . $jwt;
            $response = $response->withAddedHeader("Set-Cookie", $cookie);

            $uri = $router->pathFor("backend_dashboard");
        } else {
            $uri = $router->pathFor("backend_login");
        }

        return $response->withRedirect($uri, 302);
    }
}
