<?php
/**
 * Created by PhpStorm.
 * User: Alexander Wegner
 * Date: 07.12.2018
 * Time: 09:33
 */

namespace Oforge\Engine\Modules\Auth\Services;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;
use Oforge\Engine\Modules\Auth\Models\User\BackendUser;
use Oforge\Engine\Modules\Auth\Models\User\User;
use Oforge\Engine\Modules\Core\Abstracts\AbstractDatabaseAccess;

/**
 * This Base class has the default methods for logging users in and validating passwords.
 * It can be extended by specific LoginServices e.g. for the backend or the portal
 * Class BaseLoginService
 *
 * @package Oforge\Engine\Modules\Auth\Services
 */
class BaseLoginService extends AbstractDatabaseAccess {

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
     * @throws \Oforge\Engine\Modules\Core\Exceptions\ServiceNotFoundException
     */
    public function login(string $email, string $password) {
        /**
         * @var $authService AuthService
         */
        $authService = Oforge()->Services()->get("auth");

        /**
         * @var $passwordService PasswordService
         */
        $passwordService = Oforge()->Services()->get("password");

        /**
         * @var BackendUser|User $user
         */
        $user = $this->repository()->findOneBy(["email" => $email]);

        if (isset($user)) {
            if ($passwordService->validate($password, $user->getPassword())) {
                $userObj = $user->toArray();
                unset($userObj["password"]);

                $userObj["type"] = get_class($user);
                if (get_class($user) == BackendUser::class) {
                    $userObj["role"] = $user->getRole();
                }

                return $authService->createJWT($userObj);
            }
        }

        return null;
    }
}
