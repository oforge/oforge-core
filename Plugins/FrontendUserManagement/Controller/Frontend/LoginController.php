<?php

namespace FrontendUserManagement\Controller\Frontend;

use FrontendUserManagement\Services\FrontendUserLoginService;
use Oforge\Engine\Modules\Core\Abstracts\AbstractController;
use Oforge\Engine\Modules\Core\Annotation\Endpoint\EndpointAction;
use Oforge\Engine\Modules\Core\Annotation\Endpoint\EndpointClass;
use Oforge\Engine\Modules\Core\Exceptions\ServiceNotFoundException;
use Oforge\Engine\Modules\Core\Helper\ArrayHelper;
use Oforge\Engine\Modules\Core\Services\RedirectService;
use Oforge\Engine\Modules\Core\Services\Session\SessionManagementService;
use Oforge\Engine\Modules\I18n\Helper\I18N;
use Slim\Http\Request;
use Slim\Http\Response;
use Slim\Router;

/**
 * Class LoginController
 *
 * @package FrontendUserManagement\Controller\Frontend
 * @EndpointClass(path="/login", name="frontend_login", assetScope="Frontend")
 */
class LoginController extends AbstractController {

    /**
     * @param Request $request
     * @param Response $response
     *
     * @throws ServiceNotFoundException
     * @EndpointAction()
     */
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
     * @throws ServiceNotFoundException
     * @EndpointAction()
     */
    public function processAction(Request $request, Response $response) {
        if (empty($_SESSION)) {
            // TODO: do something not so stupid like this.
            print_r('No session :/');
            die();
        }

        /**
         * @var FrontendUserLoginService $loginService
         * @var Router $router
         * @var RedirectService $redirectService
         */
        $loginService    = Oforge()->Services()->get('frontend.user.management.login');
        $router          = Oforge()->App()->getContainer()->get('router');
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
            Oforge()->View()->Flash()->addMessage('warning', I18N::translate('invalid_direct_page_call', 'Direct page call is not allowed.'));

            return $response->withRedirect($uri, 302);
        }

        $body     = $request->getParsedBody();
        $jwt      = null;
        $referrer = ArrayHelper::get($body, 'frontend_login_referrer');
        if (isset($referrer)) {
            $uri = $referrer;
        }

        /**
         * no token was sent
         */
        if (!isset($body['token']) || empty($body['token'])) {
            Oforge()->View()->Flash()->addMessage('warning', I18N::translate('form_invalid_token', 'The data has been sent from an invalid form.'));
            Oforge()->Logger()->get()->addWarning('Someone tried to do a backend login with a form without csrf token! Redirecting to backend login.');

            return $response->withRedirect($uri, 302);
        }

        /**
         * invalid token was sent
         */
        if (!hash_equals($_SESSION['token'], $body['token'])) {
            Oforge()->View()->Flash()->addMessage('warning', I18N::translate('form_invalid_token', 'The data has been sent from an invalid form.'));
            Oforge()->Logger()->get()->addWarning('Someone tried a backend login without a valid form csrf token! Redirecting back to login.');

            return $response->withRedirect($uri, 302);
        }

        /**
         * no email or password body was sent
         */
        if (!array_key_exists('frontend_login_email', $body)
            || !array_key_exists('frontend_login_password', $body)) {
            Oforge()->View()->Flash()->addMessage('warning', I18N::translate('login_invalid_data', 'Invalid username or password.'));

            return $response->withRedirect($router->pathFor('frontend_login'), 302);
        }

        $jwt = $loginService->login($body['frontend_login_email'], $body['frontend_login_password']);

        /**
         * $jwt is null if the login credentials are incorrect
         */
        if (!isset($jwt)) {
            Oforge()->View()->Flash()->addMessage('warning', I18N::translate('login_invalid_data', 'Invalid username or password.'));

            return $response->withRedirect($uri, 302);
        }

        /** @var SessionManagementService $sessionManagement */
        $sessionManagement = Oforge()->Services()->get('session.management');
        $sessionManagement->regenerateSession();

        $_SESSION['auth']           = $jwt;
        $_SESSION['user_logged_in'] = true;

        if (!isset($referrer)) {
            $uri = $router->pathFor('frontend_account_dashboard');
        }
        Oforge()->View()->Flash()->addMessage('success', I18N::translate('login_success', 'You have successfully logged in!'));

        return $response->withRedirect($uri, 302);
    }

}
