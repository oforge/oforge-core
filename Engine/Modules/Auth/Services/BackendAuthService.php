<?php
/**
 * Created by PhpStorm.
 * User: Alexander Wegner
 * Date: 06.12.2018
 * Time: 11:11
 */

namespace Oforge\Engine\Modules\Auth\Services;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;
use Oforge\Engine\Modules\Auth\Models\User\BackendUser;

/**
 * Class BackendAuthService
 * @package Oforge\Engine\Modules\Auth\Services
 */
class BackendAuthService extends BaseAuthService {
    /**
     * @var $em EntityManager
     */
    private $em;
    
    /**
     * @var $repo EntityRepository
     */
    private $repo;
    
    /**
     * BackendAuthService constructor.
     */
    public function __construct() {
        $this->em = Oforge()->DB()->getManager();
        $this->repo = $this->em->getRepository( BackendUser::class );
    }
    
    /**
     * Validate login credentials against entities in the database and if valid, respond with a JWT.
     *
     * @param string $email
     * @param string $password
     *
     * @return string|null
     */
    public function login(string $email, string $password) {
        /**
         * @var $user BackendUser
         */
        $user = $this->repo->findOneBy( [ "email" => $email ] );
        
        if (isset($user)) {
            if ($this->comparePasswords($password, $user->getPassword())) {
                return $this->createJWT($user->getId());
            }
        }
        return null;
    }
}
