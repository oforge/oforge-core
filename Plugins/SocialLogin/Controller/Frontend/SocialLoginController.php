<?php

namespace SocialLogin\Controller\Frontend;

use Oforge\Engine\Modules\Core\Annotation\Endpoint\EndpointAction;
use Oforge\Engine\Modules\Core\Annotation\Endpoint\EndpointClass;
use Oforge\Engine\Modules\Core\Helper\StringHelper;
use Oforge\Engine\Modules\Core\Services\RedirectService;
use Oforge\Engine\Modules\Core\Services\Session\SessionManagementService;
use Oforge\Engine\Modules\I18n\Helper\I18N;
use Slim\Http\Request;
use Slim\Http\Response;
use Slim\Router;
use SocialLogin\Services\LoginConnectService;
use SocialLogin\Services\UserLoginService;

/**
 * Class SocialLoginController
 *
 * @package SocialLogin\Controller\Frontend
 * @EndpointClass(path="/social-login", name="frontend_social_login", assetScope="Frontend")
 */
class SocialLoginController {

    /**
     * @param Request $request
     * @param Response $response
     *
     * @return Response
     * @EndpointAction()
     */
    public function indexAction(Request $request, Response $response) {
        /**
         * @var Router $router
         * @var RedirectService $redirectService
         */
        $router          = Oforge()->App()->getContainer()->get('router');
        $redirectService = Oforge()->Services()->get('redirect');
        $redirectUrlName = 'frontend_login';

        $uri = null;
        if ($redirectService->hasRedirectUrlName()) {
            $redirectUrlName = $redirectService->getRedirectUrlName();
        }

        $uri  = $router->pathFor($redirectUrlName);
        $host = ($_SERVER['HTTPS'] == 'on' ? 'https://' : 'http://') . $_SERVER['HTTP_HOST'];
        if (isset($_GET['type'])) {
            $type = $_GET['type'];

            /**  @var LoginConnectService $service */
            $service = Oforge()->Services()->get('sociallogin');

            $body = $request->getParsedBody();
            $jwt  = null;

            if (isset($_SERVER['HTTP_REFERER']) && StringHelper::startsWith($_SERVER['HTTP_REFERER'], $host)
                && !(StringHelper::contains($_SERVER['HTTP_REFERER'], 'login') || StringHelper::contains($_SERVER['HTTP_REFERER'], 'register'))) {
                $referrer = $_SERVER['HTTP_REFERER'];
                $uri      = $referrer;

                $_SESSION['login_redirect_url'] = $uri;
            }

            $redirectUri = $host . $router->pathFor('frontend_social_login') . '?type=' . $type;

            if ($service->connect($type, $redirectUri)) {
                $profile = $service->getAdapter($type);

                /** @var UserLoginService $loginService */
                $loginService = Oforge()->Services()->get('sociallogin.login');
                $userProfile  = null;
                $jwt          = null;

                $index = isset($_GET['retry']) ? intval($_GET['retry']) : 0;

                try {
                    $index++;
                    $userProfile = $profile->getUserProfile();
                    $jwt         = $loginService->loginOrRegister($userProfile, $type);
                } catch (\Exception $e) {
                    if ($index < 10) {
                        return $response->withRedirect($redirectUri . '&retry=' . $index, 302);
                    }
                }

                /**
                 * Check credentials ($jwt is null if the login credentials are incorrect)
                 */
                if ($jwt === null) {
                    Oforge()->View()->Flash()->addMessage('warning', I18N::translate('login_social_account_not_compatible', [
                        'en' => 'Your account does not meet the requirements to use this function.',
                        'de' => 'Ihr Account erfüllt nicht die Voraussetzungen, um diese Funktion zu nutzen.',
                    ]));

                    return $response->withRedirect($uri, 302);
                }

                /** @var SessionManagementService $sessionManagement */
                $sessionManagement = Oforge()->Services()->get('session.management');
                $sessionManagement->regenerateSession();

                $_SESSION['auth']           = $jwt;
                $_SESSION['user_logged_in'] = true;

                if (!isset($referrer)) {
                    if (isset($_SESSION['login_redirect_url'])) {
                        $referrer = $_SESSION['login_redirect_url'];
                        $uri      = $referrer;
                        unset($_SESSION['login_redirect_url']);
                    }
                }

                if (!isset($referrer)) {
                    $uri = $router->pathFor('frontend_account_dashboard');
                }
                Oforge()->View()->Flash()->addMessage('success', I18N::translate('login_success', [
                    'en' => 'You have successfully logged in!',
                    'de' => 'Sie haben sich erfolgreich angemeldet!',
                ]));

                Oforge()->View()->assign(['cookie_consent' => true]);

                return $response->withRedirect($uri, 302);
            }
        }

        Oforge()->View()->Flash()->addMessage('error', I18N::translate('login_social_account', [
            'en' => 'Social login provider not available.',
            'de' => 'Der genutzte Provider ist nicht verfügbar.',
        ]));

        return $response->withRedirect($uri, 302);
    }

}
