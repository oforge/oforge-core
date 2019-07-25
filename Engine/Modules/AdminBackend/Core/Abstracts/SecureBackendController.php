<?php

namespace Oforge\Engine\Modules\AdminBackend\Core\Abstracts;

use Oforge\Engine\Modules\Auth\Controller\SecureController;
use Oforge\Engine\Modules\Auth\Models\User\BackendUser;

/**
 * Class SecureBackendController
 *
 * @package Oforge\Engine\Modules\AdminBackend\Core\Abstracts
 */
class SecureBackendController extends SecureController {
    /** @var string $secureControllerUserClass */
    protected $secureControllerUserClass = BackendUser::class;
}
