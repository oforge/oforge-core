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
        $response = $response->withStatus(404);
        return $response;
        
        
        
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
        $uri = $request->getUri();
        if (isset($jwt)) {
            $cookie = "authorization=" . $jwt;
            $response = $response->withAddedHeader("Set-Cookie", $cookie);
            $uri = $uri->withPath('/backend/dashboard');
            $response = $response->withRedirect((string)$uri);
        } else {
            $uri = $uri->withPath('/backend/login');
            return $response->withRedirect((string)$uri);
        }
        return $response;
    }
}
