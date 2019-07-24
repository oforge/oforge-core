<?php

namespace Oforge\Engine\Modules\AdminBackend\Core\Controller\Backend;

use Exception;
use Oforge\Engine\Modules\Auth\Services\BackendLoginService;
use Oforge\Engine\Modules\Core\Abstracts\AbstractController;
use Oforge\Engine\Modules\Core\Annotation\Endpoint\EndpointAction;
use Oforge\Engine\Modules\Core\Annotation\Endpoint\EndpointClass;
use Oforge\Engine\Modules\Core\Exceptions\ServiceNotFoundException;
use Oforge\Engine\Modules\Core\Helper\RouteHelper;
use Oforge\Engine\Modules\Core\Services\Session\SessionManagementService;
use Slim\Http\Request;
use Slim\Http\Response;

/**
 * Class LoginController
 *
 * @package Oforge\Engine\Modules\AdminBackend\Core\Controller\Backend
 * @EndpointClass(path="/backend/login", name="backend_login", assetScope="Backend")
 */
class LoginController extends AbstractController {

    /**
     * @param Request $request
     * @param Response $response
     *
     * @throws Exception
     * @EndpointAction()
     */
    public function indexAction(Request $request, Response $response) {
        // for creating a user if no user exists in dev environment:
        // $2y$10$fnI/7By7ojrwUv51JRi.K.yskzFSy0N4iiE6VheIJUh6ln1EsYWSi <<<<< geheim
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

        // disallow direct processAction call. Only post action is allowed
        if (!$request->isPost()) {
            return RouteHelper::redirect($response, 'backend_login');
        }

        $postData = $request->getParsedBody();
        $jwt      = null;
        // no token was sent
        if (!isset($postData['token']) || empty($postData['token'])) {
            Oforge()->Logger()->get()->addWarning('Someone tried to do a backend login with a form without csrf token! Redirecting to backend login.');

            return RouteHelper::redirect($response, 'backend_login');
        }
        // invalid token was sent
        if (!hash_equals($_SESSION['token'], $postData['token'])) {
            Oforge()->Logger()->get()->addWarning('Someone tried a backend login without a valid form csrf token! Redirecting back to login.');

            return RouteHelper::redirect($response, 'backend_login');
        }
        // no email or password body was sent
        if (!isset($postData['email']) || !isset($postData['password'])) {
            return RouteHelper::redirect($response, 'backend_login');
        }
        try {
            $jwt = $backendLoginService->login($postData['email'], $postData['password']);// $jwt is null if the login credentials are incorrect
            if (!isset($jwt)) {
                return RouteHelper::redirect($response, 'backend_login');
            }
        } catch (Exception $exception) {
            Oforge()->Logger()->logException($exception);

            return RouteHelper::redirect($response, 'backend_login');
        }

        /** @var SessionManagementService $sessionManagement */
        $sessionManagement = Oforge()->Services()->get('session.management');
        $sessionManagement->regenerateSession();

        $_SESSION['auth'] = $jwt;

        if (isset($_SESSION["login_redirect_url"])) {
            $referrer = $_SESSION["login_redirect_url"];
            unset($_SESSION["login_redirect_url"]);

            return $response->withRedirect($referrer, 302);
        }

        return RouteHelper::redirect($response, 'backend_dashboard');
    }

}
