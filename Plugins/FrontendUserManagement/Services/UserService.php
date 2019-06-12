<?php

namespace FrontendUserManagement\Services;

use Doctrine\ORM\ORMException;
use FrontendUserManagement\Models\User;
use Oforge\Engine\Modules\Core\Abstracts\AbstractDatabaseAccess;

class UserService extends AbstractDatabaseAccess {
    public function __construct() {
        parent::__construct(['default' => User::class]);
    }

    /**
     * @param $id
     *
     * @return User
     * @throws ORMException
     */
    public function getUserById(int $id) : ?User {
        /** @var User $user */
        $user = $this->repository()->find($id);

        return $user;
    }

    /**
     * @return array
     * @throws ORMException
     */
    public function getUsers() : array {
        $users  = $this->repository()->findAll();
        $result = [];
        foreach ($users as $user) {
            $result[] = $user->toArray();
        }

        return $result;
    }
}
