<?php

namespace FrontendUserManagement\Services;

use Doctrine\ORM\ORMException;
use FrontendUserManagement\Models\User;
use Oforge\Engine\Modules\Auth\Models\User\BaseUser;
use Oforge\Engine\Modules\Auth\Services\BaseLoginService;
use Oforge\Engine\Modules\Auth\Services\PasswordService;
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
     * @param string $email
     *
     * @return int status: 0 => user doesn't exists
     *                     1 => user not active
     *                     2 => user exists and is active
     * @throws ORMException
     */
    public function getUserStatus(string $email) {
        /** @var User $user */
        $user = $this->repository()->findOneBy([
            'email' => $email,
        ]);
        if (isset($user)) {
            if ($user->isActive()) {
                return 2;
            }
            return 1;
        }
        return 0;
    }
}
