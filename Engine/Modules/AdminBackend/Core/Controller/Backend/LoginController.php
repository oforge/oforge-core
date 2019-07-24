<?php

namespace Oforge\Engine\Modules\AdminBackend\Core\Controller\Backend;

use Exception;
use Oforge\Engine\Modules\AdminBackend\Core\Abstracts\SecureBackendController;
use Oforge\Engine\Modules\Auth\Models\User\BackendUser;
use Oforge\Engine\Modules\Auth\Services\BackendLoginService;
use Oforge\Engine\Modules\Core\Annotation\Endpoint\EndpointAction;
use Oforge\Engine\Modules\Core\Annotation\Endpoint\EndpointClass;
use Oforge\Engine\Modules\Core\Exceptions\ServiceNotFoundException;
use Oforge\Engine\Modules\Core\Helper\RouteHelper;
use Oforge\Engine\Modules\Core\Services\Session\SessionManagementService;
use Slim\Http\Request;
use Slim\Http\Response;
use Slim\Router;

/**
 * Class LoginController
 *
 * @package Oforge\Engine\Modules\AdminBackend\Core\Controller\Backend
 * @EndpointClass(path="/backend/login", name="backend_login", assetScope="Backend")
 */
class LoginController extends SecureBackendController {

    public function initPermissions() {
        $this->ensurePermissions('indexAction', BackendUser::class, BackendUser::ROLE_PUBLIC);
        $this->ensurePermissions('processAction', BackendUser::class, BackendUser::ROLE_PUBLIC);
    }

    /**
     * @param Request $request
     * @param Response $response
     *
     * @return Response
     * @throws Exception
     * @EndpointAction()
     */
    public function indexAction(Request $request, Response $response) {
        // for creating a user if no user exists in dev environment:
        // $2y$10$fnI/7By7ojrwUv51JRi.K.yskzFSy0N4iiE6VheIJUh6ln1EsYWSi <<<<< geheim
        if (isset($_SESSION['auth']) && !isset($_SESSION['login_redirect_url'])) {
            return RouteHelper::redirect($response, 'backend_dashboard');
        }

        return $response;
    }

    /**
     * Login Action
     *
     * @param Request $request
     * @param Response $response
     *
     * @return Response
     * @throws ServiceNotFoundException
     * @EndpointAction()
     */
    public function processAction(Request $request, Response $response) {
        if (empty($_SESSION)) {
            print_r('No session :/');
            die();
        }
        /** @var BackendLoginService $backendLoginService */
        $backendLoginService = Oforge()->Services()->get('backend.login');
        /** @var Router $router */
        $router = Oforge()->App()->getContainer()->get('router');
        $uri    = $router->pathFor('backend_login');

        /**
         * disallow direct processAction call. Only post action is allowed
         */
        if (!$request->isPost()) {
            return $response->withRedirect($uri, 302);
        }

        $body = $request->getParsedBody();
        $jwt  = null;

        /**
         * no token was sent
         */
        if (!isset($body['token']) || empty($body['token'])) {
            Oforge()->Logger()->get()->addWarning('Someone tried to do a backend login with a form without csrf token! Redirecting to backend login.');

            return $response->withRedirect($uri, 302);
        }

        /**
         * invalid token was sent
         */
        if (!hash_equals($_SESSION['token'], $body['token'])) {
            Oforge()->Logger()->get()->addWarning('Someone tried a backend login without a valid form csrf token! Redirecting back to login.');

            return $response->withRedirect($uri, 302);
        }

        /**
         * no email or password body was sent
         */
        if (!isset($body['email']) || !isset($body['password'])) {
            return $response->withRedirect($router->pathFor('backend_login'), 302);
        }

        $jwt = $backendLoginService->login($body['email'], $body['password']);

        /**
         * $jwt is null if the login credentials are incorrect
         */
        if (!isset($jwt)) {
            return $response->withRedirect($uri, 302);
        }

        /** @var SessionManagementService $sessionManagement */
        $sessionManagement = Oforge()->Services()->get('session.management');
        $sessionManagement->regenerateSession();

        $_SESSION['auth'] = $jwt;

        $referrer = null;
        if (isset($_SESSION["login_redirect_url"])) {
            $referrer = $_SESSION["login_redirect_url"];
            unset($_SESSION["login_redirect_url"]);
        }

        if ($referrer != null) {
            $uri = $referrer;
        } else {
            $uri = $router->pathFor('backend_dashboard');
        }

        return $response->withRedirect($uri, 302);
    }

}
