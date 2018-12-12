<?php

namespace Oforge\Engine\Modules\AdminBackend\Abstracts;

use Oforge\Engine\Modules\Core\Abstracts\AbstractController;

class SecureBackendController extends AbstractController
{
    protected function ensurePermissions($method, $userType, $role)
    {
        Oforge()->Services()->get("permissions")->put(get_called_class() . ":" . $method, $userType, $role);
    }

    protected function initPermissions()
    {

    }
}
