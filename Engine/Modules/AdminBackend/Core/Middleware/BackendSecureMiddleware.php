<?php

namespace Oforge\Engine\Modules\AdminBackend\Core\Middleware;

use Oforge\Engine\Modules\Auth\Middleware\SecureMiddleware;

class BackendSecureMiddleware extends SecureMiddleware
{
    protected $urlPathName = 'backend_login';
}
