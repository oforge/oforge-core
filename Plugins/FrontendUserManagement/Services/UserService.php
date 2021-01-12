<?php

namespace FrontendUserManagement\Services;

use Doctrine\ORM\ORMException;
use FrontendUserManagement\Models\User;
use FrontendUserManagement\Models\UserAddress;
use FrontendUserManagement\Models\UserDetail;
use Insertion\Models\InsertionProfile;
use Oforge\Engine\Modules\Core\Abstracts\AbstractDatabaseAccess;
use Oforge\Engine\Modules\I18n\Helper\I18N;

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

    public function getAnonymous() : User {
        $guid = "00000000-0000-0000-0000-000000000001";
        $user = $this->repository()->findOneBy(["guid" => $guid]);
        if ($user == null) {
            $user = new User();
            $user->setEmail(I18N::translate("anonymous"));
            $user->setPassword('');
            $this->entityManager()->create($user);
            $user->setGuid($guid);
            $this->entityManager()->update($user);
            $userDetails = UserDetail::create(['user' => $user, "nickname" => I18N::translate("anonymous")]);
            $userAddress = UserAddress::create(['user' => $user]);
            $userProfile = InsertionProfile::create(['user' => $user]);
            $this->entityManager()->create($userProfile);
            $this->entityManager()->create($userAddress);
            $this->entityManager()->create($userDetails);
        }

        return $user;
    }
}
