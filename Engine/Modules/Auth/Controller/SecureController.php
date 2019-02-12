<?php

namespace Oforge\Engine\Modules\Auth\Controller;

use Oforge\Engine\Modules\Auth\Services\Permissions;
use Oforge\Engine\Modules\Core\Abstracts\AbstractController;

class SecureController extends AbstractController {
    /**
     * @param $method
     * @param $userType
     * @param int|null $role
     *
     * @throws \Oforge\Engine\Modules\Core\Exceptions\ServiceNotFoundException
     */
    protected function ensurePermissions($method, $userType, $role = null)
    {
        /** @var Permissions $permissions */
        $permissions = Oforge()->Services()->get("permissions");
        $permissions->put(get_called_class() . ":" . $method, $userType, $role);
    }
    
    public function initPermissions() {}
}