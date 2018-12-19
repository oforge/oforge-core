<?php
/**
 * Created by PhpStorm.
 * User: Alexander Wegner
 * Date: 19.12.2018
 * Time: 12:51
 */

namespace Oforge\Engine\Modules\UserManagement\Controller\Backend;

use Oforge\Engine\Modules\Auth\Services\AuthService;
use Oforge\Engine\Modules\Session\Services\SessionManagementService;
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
     * Update one's own user profile.
     * If the password hasn't been changed (post value is empty) the password part gets removed, so it won't be updated.
     * The new user data has to be updated also in the session. A user cannot change his/her own role
     *
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

            if (key_exists("password", $user) && $user["password"] == "") {
                unset($user["password"]);
            }

            /**
             * @var $authService AuthService
             */
            $authService = Oforge()->Services()->get("auth");
            $oldUser = $authService->decode($_SESSION["auth"]);

            $user["type"] = $oldUser["type"];
            $user["role"] = $oldUser["role"];

            $backendUserService->update($user);

            /**
             * @var $sessionManagement SessionManagementService
             */
            $sessionManagement = Oforge()->Services()->get('session.management');
            $sessionManagement->regenerateSession();

            $_SESSION["auth"] = $authService->createJWT($user);
        }

        /**
         * @var $router Router
         */
        $router = Oforge()->App()->getContainer()->get("router");
        return $response->withRedirect($router->pathFor("backend_profile"), 302);
    }
}
