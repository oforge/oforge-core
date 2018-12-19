<?php
/**
 * Created by PhpStorm.
 * User: Alexander Wegner
 * Date: 19.12.2018
 * Time: 12:51
 */

namespace Oforge\Engine\Modules\UserManagement\Controller\Backend;

use Oforge\Engine\Modules\Auth\Services\AuthService;
use Oforge\Engine\Modules\UserManagement\Services\BackendUsersCrudService;
use Slim\Http\Request;
use Slim\Http\Response;
use Slim\Router;

class ProfileController {
    /**
     * @param Request $request
     * @param Response $response
     *
     * @throws \Oforge\Engine\Modules\Core\Exceptions\ServiceNotFoundException
     */
    public function indexAction(Request $request, Response $response) {
        /**
         * @var $authService AuthService
         */
        $authService = Oforge()->Services()->get("auth");
        $jwt = $_SESSION["auth"];
        $user = $authService->decode($jwt);
        Oforge()->View()->assign(["user" => $user]);
    }
    
    /**
     * @param Request $request
     * @param Response $response
     *
     * @return Response
     * @throws \Oforge\Engine\Modules\Core\Exceptions\NotFoundException
     * @throws \Oforge\Engine\Modules\Core\Exceptions\ServiceNotFoundException
     */
    public function updateAction(Request $request, Response $response) {
        if ($request->isPost()) {
            /**
             * @var $backendUserService BackendUsersCrudService
             */
            $backendUserService = Oforge()->Services()->get("backend.users.crud");
            $user = $request->getParsedBody();
            if(key_exists("password", $user) && $user["password"] == "") {
                unset($user["password"]);
            }
            $backendUserService->update($user);
        }
        /**
         * @var $router Router
         */
        $router = Oforge()->App()->getContainer()->get("router");
        return $response->withRedirect($router->pathFor("backend_profile"), 302);
    }
}