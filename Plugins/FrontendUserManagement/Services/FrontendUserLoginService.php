<?php

namespace FrontendUserManagement\Services;

use FrontendUserManagement\Models\User;
use Oforge\Engine\Modules\Auth\Services\BaseLoginService;

/**
 * Class FrontendUserLoginService
 *
 * @package FrontendUserManagement\Services
 */
class FrontendUserLoginService extends BaseLoginService {

    /**
     * FrontendUserLoginService constructor.
     */
    public function __construct() {
        parent::__construct(User::class);
    }

}
