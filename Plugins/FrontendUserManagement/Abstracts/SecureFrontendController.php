<?php

namespace FrontendUserManagement\Abstracts;

use FrontendUserManagement\Models\User;
use Oforge\Engine\Modules\Auth\Controller\SecureController;

/**
 * Class SecureFrontendController
 *
 * @package FrontendUserManagement\Abstracts
 */
class SecureFrontendController extends SecureController {
    /** @var string $secureControllerUserClass */
    protected $secureControllerUserClass = User::class;
}
