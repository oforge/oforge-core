<?php

namespace Oforge\Engine\Modules\AdminBackend\Core\Middleware;

use Oforge\Engine\Modules\Auth\Middleware\SecureMiddleware;
use Oforge\Engine\Modules\Auth\Models\User\BackendUser;

/**
 * Class BackendSecureMiddleware
 *
 * @package Oforge\Engine\Modules\AdminBackend\Core\Middleware
 */
class BackendSecureMiddleware extends SecureMiddleware {
    /** @var string $userClass */
    protected $userClass = BackendUser::class;
    /** @var string $invalidRedirectPathName */
    protected $invalidRedirectPathName = 'backend_login';

    /** @inheritDoc */
    protected function checkPermission(?array $user, ?array $permission) {
        return $permission !== null#
               && ($permission['role'] === BackendUser::ROLE_PUBLIC
                   || (#
                       $user !== null && isset($user['role']) && isset($user['type'])
                       && $user['type'] === $permission['type']
                       && $user['role'] <= $permission['role']#
                   )#
               );
    }

}
