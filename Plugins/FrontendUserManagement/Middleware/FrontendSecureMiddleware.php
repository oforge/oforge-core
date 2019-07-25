<?php

namespace FrontendUserManagement\Middleware;

use FrontendUserManagement\Models\User;
use Oforge\Engine\Modules\Auth\Middleware\SecureMiddleware;
use Oforge\Engine\Modules\I18n\Helper\I18N;

/**
 * Class FrontendSecureMiddleware
 *
 * @package FrontendUserManagement\Middleware
 */
class FrontendSecureMiddleware extends SecureMiddleware {
    /** @var string $userClass */
    protected $userClass = User::class;
    /** @var string $viewUserDataKey */
    protected $viewUserDataKey = 'current_user';
    /** @var string $invalidRedirectPathName */
    protected $invalidRedirectPathName = 'frontend_login';

    /** @inheritDoc */
    protected function createPermissionDeniedFlashMessage() {
        Oforge()->View()->Flash()->addMessage('error', I18N::translate('secured_area_no_login', [
            'en' => 'You have to be logged in to the requested page.',
            'de' => 'Du must für die aufgerufene Seite angemeldet sein.',
        ]));
    }

    /** @inheritDoc */
    public static function checkUserPermission($user, $permission) {
        // The frontend user has no role (yet). So we don't check the role.
        return ($user !== null && isset($user['type']) && $user['type'] === $permission['type']);
    }

}
