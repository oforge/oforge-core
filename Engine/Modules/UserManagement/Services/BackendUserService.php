<?php

namespace Oforge\Engine\Modules\UserManagement\Services;

use Exception;
use Oforge\Engine\Modules\Auth\Enums\InvalidPasswordFormatException;
use Oforge\Engine\Modules\Auth\Models\User\BackendUser;
use Oforge\Engine\Modules\Auth\Services\PasswordService;
use Oforge\Engine\Modules\Core\Abstracts\AbstractDatabaseAccess;
use Oforge\Engine\Modules\Core\Helper\ArrayHelper;

/**
 * Class BackendUserService
 *
 * @package Oforge\Engine\Modules\UserManagement\Services
 */
class BackendUserService extends AbstractDatabaseAccess {

    public function __construct() {
        parent::__construct([
            'BackendUser' => BackendUser::class,
        ]);
    }

    /**
     * Create backend user.
     *
     * @param string $email
     * @param string $name
     * @param string|null $password
     * @param int $role
     *
     * @return string
     * @throws Exception
     */
    public function createBackendUser(string $email, string $name, ?string $password = null, int $role = BackendUser::ROLE_ADMINISTRATOR) {
        if ($this->repository('BackendUser')->count(['email' => $email]) > 0) {
            return "User  with email '$email' already exists.";
        }

        /** @var PasswordService $passwordService */
        $passwordService = new PasswordService();
        if ($password === null) {
            try {
                $password = $passwordService->generatePassword();
            } catch (Exception $exception) {
                Oforge()->Logger()->logException($exception);
                throw $exception;
            }
        } else {
            try {
                $passwordService->validateFormat($password);
            } catch (InvalidPasswordFormatException $exception) {
                return 'Password format is not valid: ' . $exception->getMessage();
            }
        }
        $passwordHash = $passwordService->hash($password);

        $backendUser = BackendUser::create([
            'email'    => $email,
            'name'     => $name,
            'role'     => $role,
            'password' => $passwordHash,
            'active'   => true,
        ]);
        $this->entityManager()->create($backendUser);

        $role = ArrayHelper::get([
            BackendUser::ROLE_SYSTEM        => 'system',
            BackendUser::ROLE_ADMINISTRATOR => 'admin',
            BackendUser::ROLE_MODERATOR     => 'moderator',
        ], $role, $role);

        return "User ($role) created with email '$email' and password: " . $password;
    }

}
