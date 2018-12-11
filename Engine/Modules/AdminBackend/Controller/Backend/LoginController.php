<?php

namespace Oforge\Engine\Modules\AdminBackend\Controller\Backend;

use \Oforge\Engine\Modules\Core\Abstracts\AbstractController;
use Slim\Http\Request;
use Slim\Http\Response;

class LoginController extends AbstractController
{
    /**
     * @param Request $request
     * @param Response $response
     *
     * @return Response
     * @throws \Oforge\Engine\Modules\Core\Exceptions\ServiceNotFoundException
     */
    public function indexAction(Request $request, Response $response)
    {
        //  /backend/login

    }

    /**
     * Login Port Action
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
        //backend/login/process

        $backendLoginService = Oforge()->Services()->get('backend.login');

        print_r($request->getParsedBody());
        die();

        $jwt = $backendLoginService->login("aw@7pkonzepte.de", "geheim");
        if (isset($jwt)) {
            $cookie = "authorization=" . $jwt;
            $response = $response->withAddedHeader("Set-Cookie", $cookie);
            Oforge()->View()->assign(["greeting" => "Login"]);
        }
        return $response;
    }
}
