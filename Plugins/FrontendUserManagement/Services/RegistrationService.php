<?php

namespace FrontendUserManagement\Services;

use FrontendUserManagement\Models\User;
use Oforge\Engine\Modules\Core\Abstracts\AbstractDatabaseAccess;

class RegistrationService extends AbstractDatabaseAccess {

    public function __construct() {
        parent::__construct(['default' => User::class]);
    }

    /**
     * @param string $email
     * @param string $password
     *
     * @return array|null
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function register(string $email, string $password) {
        $user = null;

        if (!$this->userExists($email)) {
            $user = new User();
            $user->setEmail($email);
            $user->setPassword($password);
            $this->entityManager()->persist($user);
            $this->entityManager()->flush();

            $user = $user->toArray();
            unset($user["password"]);
            $user["type"] = User::class;
        }

        return $user;
    }

    private function userExists(string $email) {
        return $this->repository()->findBy(['email' => $email]);
    }

}