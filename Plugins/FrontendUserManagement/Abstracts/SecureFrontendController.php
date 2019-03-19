<?php

namespace FrontendUserManagement\Abstracts;

use Oforge\Engine\Modules\Auth\Controller\SecureController;

class SecureFrontendController extends SecureController {
    protected function ensurePermissions($method, $userType, $role = null) {
        parent::ensurePermissions($method, $userType, $role = null);
    }
}
