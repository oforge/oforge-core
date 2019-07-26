<?php

namespace Oforge\Engine\Modules\Auth\Middleware;

use Oforge\Engine\Modules\Auth\Controller\SecureController;
use Oforge\Engine\Modules\Auth\Models\User\BaseUser;
use Oforge\Engine\Modules\Auth\Services\AuthService;
use Oforge\Engine\Modules\Auth\Services\PermissionService;
use Oforge\Engine\Modules\Core\Exceptions\ServiceNotFoundException;
use Oforge\Engine\Modules\Core\Helper\RouteHelper;
use Oforge\Engine\Modules\I18n\Helper\I18N;
use Slim\Http\Request;
use Slim\Http\Response;

/**
 * Class SecureMiddleware
 *
 * @package Oforge\Engine\Modules\Auth\Middleware
 */
class SecureMiddleware {
    /** @var string $userClass */
    protected $userClass = BaseUser::class;
    /** @var string $viewUserDataKey */
    protected $viewUserDataKey = 'user';
    /** @var string $invalidRedirectPathName The named path for redirects */
    protected $invalidRedirectPathName = '';

    /**
     * Middleware call before the controller call
     *
     * @param Request $request
     * @param Response $response
     *
     * @return Response|null
     * @throws ServiceNotFoundException
     */
    public function prepend(Request $request, Response $response) : ?Response {
        $user = null;
        if (isset($_SESSION['auth'])) {
            $auth = $_SESSION['auth'];
            /** @var AuthService $authService */
            $authService = Oforge()->Services()->get('auth');
            $user        = $authService->decode($auth);
            if ($user !== null && $user['type'] === $this->userClass) {
                Oforge()->View()->assign([
                    $this->viewUserDataKey => $user,
                ]);
            }
        }
        $routeController  = Oforge()->View()->get('meta')['route'];
        $controllerClass  = $routeController['controllerClass'];
        $controllerMethod = $routeController['controllerMethod'];
        if (is_subclass_of($controllerClass, SecureController::class)) {
            /** @var PermissionService $permissionService */
            $permissionService = Oforge()->Services()->get('permissions');
            $permission        = $permissionService->get($controllerClass, $controllerMethod);
        } else {
            $permission = ['role' => null, 'type' => $this->userClass];
        }
        if (static::checkUserPermission($user, $permission)) {
            //nothing to do. proceed
        } else {
            Oforge()->View()->assign(['stopNext' => true]);
            $_SESSION['login_redirect_url'] = $request->getUri()->getPath();
            $this->createPermissionDeniedFlashMessage();
            if (!empty($this->invalidRedirectPathName)) {
                return RouteHelper::redirect($response, $this->invalidRedirectPathName);
            }

            return $response->withRedirect('/', 302);
        }

        return $response;
    }

    /**
     * Create Flash message for failed request.
     */
    protected function createPermissionDeniedFlashMessage() {
        Oforge()->View()->Flash()->addMessage('warning', I18N::translate('secured_area_no_permission', [
            'en' => 'You are not logged in or do not have permission for this page. Log in with another user.',
            'de' => 'Du bist nicht eingeloggt oder hast keine Berechtigung für diese Seite. Logge dich mit einem anderem User ein.',
        ]));
    }

    /**
     * @param array|null $user
     * @param array|null $permission
     *
     * @return bool
     */
    public static function checkUserPermission(?array $user, ?array $permission) {
        return ($user !== null && $permission !== null
                && isset($user['role'])
                && isset($user['type'])
                && $user['type'] === $permission['type']
                && $user['role'] <= $permission['role']);
    }

}
