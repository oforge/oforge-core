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

/**
 * This Base class has the default methods for logging users in and validating passwords.
 * It can be extended by specific LoginServices e.g. for the backend or the portal
 *
 * Class BaseLoginService
 * @package Oforge\Engine\Modules\Auth\Services
 */
class BaseLoginService {
    /**
     * @var $em EntityManager
     */
    protected $em;
    
    /**
     * @var $repo EntityRepository
     */
    protected $repo;
    
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
         * @var $user BackendUser|User
         */
        $user = $this->repo->findOneBy(["email" => $email]);
        
        if (isset($user)) {
            if ($passwordService->validate($password, $user->getPassword())) {
                $userObj = [
                    'user_id' => $user->getId(),
                    'user_email' => $user->getEmail(),
                    'user_type' => get_class($user)
                ];
                
                if (get_class($user) == BackendUser::class) {
                    $userObj = array_merge($userObj, ['user_role' => $user->getRole()]);
                }
                
                return $authService->createJWT($userObj);
            }
        }
        return null;
    }
}
