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
         * @var $user BackendUser|User
         */
        $user = $this->repo->findOneBy(["email" => $email]);
        
        if (isset($user)) {
            if ($this->comparePasswords($password, $user->getPassword())) {
                $userObj = [
                    'user_id' => $user->getId(),
                    'user_email' => $user->getEmail(),
                    'user_type' => get_class($user)
                ];
                return $authService->createJWT($userObj);
            }
        }
        return null;
    }
    
    /**
     * @param string $clientPassword
     * @param string $serverPassword
     *
     * @return bool
     */
    public function comparePasswords(string $clientPassword, string $serverPassword) : bool {
        return password_verify($clientPassword, $serverPassword);
    }
}