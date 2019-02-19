<?php

namespace FrontendUserManagement\Services;

use FrontendUserManagement\Models\User;
use Oforge\Engine\Modules\Auth\Services\BaseLoginService;

class FrontendUserLoginService extends BaseLoginService {
    public function __construct() {
        parent::__construct(['default' => User::class]);
    }
}