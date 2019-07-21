<?php

namespace FrontendUserManagement\Services;

use Doctrine\ORM\ORMException;
use Exception;
use FrontendUserManagement\Models\User;
use FrontendUserManagement\Models\UserAddress;
use Oforge\Engine\Modules\Core\Abstracts\AbstractDatabaseAccess;

/**
 * Class UserAddressService
 *
 * @package FrontendUserManagement\Services
 */
class UserAddressService extends AbstractDatabaseAccess {

    public function __construct() {
        parent::__construct(["default" => UserAddress::class, "user" => User::class]);
    }

    /**
     * @param array $data
     *
     * @return bool
     */
    public function save(array $data) {
        try {
            $address = $this->get($data['userId']);

            if ($address == null) {
                $address = UserAddress::create($data);
                $user   = $this->repository("user")->find($data['userId']);
                $address->setUser($user);
                $this->entityManager()->create($address);
            } else {
                $address->fromArray($data);
                $this->entityManager()->update($address);
            }

            return true;

        } catch (Exception $ex) {
            Oforge()->Logger()->get()->addError('Could not persist / flush userDetails', ['msg' => $ex->getMessage()]);
        }

        return false;
    }

    /**
     * @param $userID
     *
     * @return UserAddress|null
     * @throws ORMException
     */
    public function get($userID) : ?UserAddress {
        /** @var UserAddress|null $address */
        $address = $this->repository()->findOneBy(['user' => $userID]);

        return $address;
    }

}
