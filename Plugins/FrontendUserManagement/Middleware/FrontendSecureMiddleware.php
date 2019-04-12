<?php
namespace FrontendUserManagement\Middleware;

use Oforge\Engine\Modules\Auth\Middleware\SecureMiddleware;

class FrontendSecureMiddleware extends SecureMiddleware
{
    protected $urlPathName = 'frontend_login';
    
    /**
     * @param $user
     * @param $permissions
     *
     * @return bool
     */
    protected function isUserValid($user, $permissions) {
        /**
         * The frontend user has no role (yet). So we don't check the role.
         */
        return (!is_null($user) && isset($user["type"]) && $user["type"] == $permissions["type"]);
    }
}
