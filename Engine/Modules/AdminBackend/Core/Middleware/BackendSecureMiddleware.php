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
    protected function isUserValid(?array $user, array $permission) {
        return ($user === null && $permission['role'] === BackendUser::ROLE_PUBLIC)
               || ($user !== null && $permission !== null
                   && isset($user['role'])
                   && isset($user['type'])
                   && $user['type'] === $permission['type']
                   && $user['role'] <= $permission['role']);
    }

}
