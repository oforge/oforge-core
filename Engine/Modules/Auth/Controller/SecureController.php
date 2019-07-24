<?php

namespace Oforge\Engine\Modules\Auth\Controller;

use Oforge\Engine\Modules\Auth\Models\User\BaseUser;
use Oforge\Engine\Modules\Auth\Services\PermissionService;
use Oforge\Engine\Modules\Core\Abstracts\AbstractController;
use Oforge\Engine\Modules\Core\Exceptions\ServiceNotFoundException;

/**
 * Class SecureController
 *
 * @package Oforge\Engine\Modules\Auth\Controller
 */
class SecureController extends AbstractController {
    /** @var string $secureControllerUserClass */
    protected $secureControllerUserClass = BaseUser::class;

    public function initPermissions() {
    }

    /**
     * @param string $method
     * @param string $userType
     * @param int|null $role
     */
    protected function ensurePermission(string $method, ?int $role = null) {
        static::ensurePermissions([$method], $role);
    }

    /**
     * @param string[] $methods
     * @param string $userType
     * @param int|null $role
     */
    protected function ensurePermissions($methods, ?int $role = null) {
        try {
            /** @var PermissionService $permissionService */
            $permissionService = Oforge()->Services()->get('permissions');
            foreach ($methods as $method) {
                $permissionService->put(static::class, $method, $this->secureControllerUserClass, $role);
            }
        } catch (ServiceNotFoundException $exception) {
            Oforge()->Logger()->logException($exception);
        }
    }

}
