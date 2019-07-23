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
    /** @var string $fallbackPermissionRole */
    protected $fallbackPermissionRole = BackendUser::ROLE_MODERATOR;
    /** @var string $urlPathName */
    protected $urlPathName = 'backend_login';
}
