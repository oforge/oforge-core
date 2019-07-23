<?php
/**
 * Created by PhpStorm.
 * User: Alexander Wegner
 * Date: 07.12.2018
 * Time: 09:33
 */

namespace Oforge\Engine\Modules\Auth\Services;

use Doctrine\ORM\ORMException;
use Oforge\Engine\Modules\Auth\Models\User\BaseUser;
use Oforge\Engine\Modules\Core\Abstracts\AbstractDatabaseAccess;
use Oforge\Engine\Modules\Core\Exceptions\ServiceNotFoundException;

/**
 * This Base class has the default methods for logging users in and validating passwords.
 * It can be extended by specific LoginServices e.g. for the backend or the portal
 * Class BaseLoginService
 *
 * @package Oforge\Engine\Modules\Auth\Services
 */
class BaseLoginService extends AbstractDatabaseAccess {

    /**
     * BaseLoginService constructor.
     *
     * @param string|array $models
     */
    public function __construct($models) {
        parent::__construct($models);
    }

    /**
     * Validate login credentials against entities in the database and if valid, respond with a JWT.
     *
     * @param string $email
     * @param string $password
     *
     * @return string|null
     * @throws ServiceNotFoundException
     * @throws ORMException
     */
    public function login(string $email, string $password) {
        /** @var AuthService $authService */
        $authService = Oforge()->Services()->get('auth');
        /** @var PasswordService $passwordService */
        $passwordService = Oforge()->Services()->get('password');
        /** @var BaseUser|null $user */
        $user = $this->repository()->findOneBy([
            'email'  => $email,
            'active' => true,
        ]);
        if (isset($user)) {
            if ($passwordService->validate($password, $user->getPassword())) {
                $userData = $user->toArray(1);
                unset($userData['password']);
                $userData['type'] = get_class($user);

                return $authService->createJWT($userData);
            }
        }

        return null;
    }

}
