<?php

namespace FrontendUserManagement\Services;

use Doctrine\ORM\ORMException;
use FrontendUserManagement\Models\User;
use Oforge\Engine\Modules\Auth\Models\User\BaseUser;
use Oforge\Engine\Modules\Auth\Services\BaseLoginService;
use Oforge\Engine\Modules\Core\Exceptions\ServiceNotFoundException;

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

    /**
     * Check if the account has been activated after validating the login information
     *
     * @param string $email
     * @param string $password
     *
     * @return string|null
     * @throws ServiceNotFoundException
     * @throws ORMException
     */

    public function isActive(string $email, string $password) {
        $passwordService = Oforge()->Services()->get('password');
        /** @var BaseUser|null $user */
        $user = $this->repository()->findOneBy([
            'email'  => $email,
        ]);

        if (isset($user)) {
            if ($passwordService->validate($password, $user->getPassword())) {
                return $user->isActive();
            }
        }

        return null;
    }
}
