<?php

namespace FrontendUserManagement\Controller\Frontend;

use FrontendUserManagement\Services\FrontendUserLoginService;
use Oforge\Engine\Modules\Core\Abstracts\AbstractController;
use Oforge\Engine\Modules\Core\Services\RedirectService;
use Oforge\Engine\Modules\Session\Services\SessionManagementService;
use Slim\Http\Request;
use Slim\Http\Response;
use Slim\Router;

class LoginController extends AbstractController {
    public function indexAction(Request $request, Response $response) {
        /** @var RedirectService $redirectService */
        $redirectService = Oforge()->Services()->get('redirect');
        $redirectService->setRedirectUrlName('frontend_login');
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
            // TODO: do something not so stupid like this.
            print_r('No session :/');
            die();
        }
    
        /** @var FrontendUserLoginService $loginService */
        $loginService = Oforge()->Services()->get('frontend.user.management.login');
    
        /**
         * @var $router Router
         */
        $router = Oforge()->App()->getContainer()->get('router');

        /** @var RedirectService $redirectService */
        $redirectService = Oforge()->Services()->get('redirect');
        $redirectUrlName = 'frontend_login';
        if ($redirectService->hasRedirectUrlName()) {
            $redirectUrlName = $redirectService->getRedirectUrlName();
        }

        $uri = $router->pathFor($redirectUrlName);
    
        /**
         * disallow direct processAction call. Only post action is allowed
         */
        if (!$request->isPost()) {
            Oforge()->View()->addFlashMessage('warning', 'Direct page call is not allowed.');
            return $response->withRedirect($uri, 302);
        }
    
        $body = $request->getParsedBody();
        $jwt = null;
    
        /**
         * no token was sent
         */
        if (!isset($body['token']) || empty($body['token'])) {
            Oforge()->Logger()->get()->addWarning('Someone tried to do a backend login with a form without csrf token! Redirecting to backend login.');
            Oforge()->View()->addFlashMessage('warning', 'The data has been sent from an invalid form.');
            return $response->withRedirect($uri, 302);
        }
    
        /**
         * invalid token was sent
         */
        if (!hash_equals($_SESSION['token'], $body['token'])) {
            Oforge()->Logger()->get()->addWarning('Someone tried a backend login without a valid form csrf token! Redirecting back to login.');
            Oforge()->View()->addFlashMessage('warning', 'The data has been sent from an invalid form.');
            return $response->withRedirect($uri, 302);
        }
    
        /**
         * no email or password body was sent
         */
        if (!array_key_exists('frontend_login_email', $body) ||
            !array_key_exists('frontend_login_password', $body)) {
            Oforge()->View()->addFlashMessage('warning', 'Invalid username or password.');
            return $response->withRedirect($router->pathFor('frontend_login'), 302);
        }
    
        $jwt = $loginService->login($body['frontend_login_email'], $body['frontend_login_password']);
    
        /**
         * $jwt is null if the login credentials are incorrect
         */
        if (!isset($jwt)) {
            Oforge()->View()->addFlashMessage('warning', 'Invalid username or password.');
            return $response->withRedirect($uri, 302);
        }
    
        /**
         * @var $sessionManagement SessionManagementService
         */
        $sessionManagement = Oforge()->Services()->get('session.management');
        $sessionManagement->regenerateSession();
    
        $_SESSION['auth'] = $jwt;
        $_SESSION['user_logged_in'] = true;
    
        $uri = $router->pathFor('frontend_profile');

        Oforge()->View()->addFlashMessage('success', 'you have successfully logged in!');
    
        return $response->withRedirect($uri, 302);
    }
}