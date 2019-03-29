<?php

namespace FrontendUserManagement\Services;


use Doctrine\ORM\ORMException;
use FrontendUserManagement\Models\UserDetail;
use Oforge\Engine\Modules\Core\Abstracts\AbstractDatabaseAccess;

class UserDetailsService extends AbstractDatabaseAccess {

    public function __construct() {
        parent::__construct(['default' => UserDetail::class]);
    }

    /**
     * @param array $userDetails
     * @return bool
     */
    public function save(array $userDetails) {
        $details = $this->get($userDetails['userId']);

        if (!$details) {
            $details = UserDetail::create();
        }

        $details->setFirstName($userDetails['firstName']);
        $details->setLastName($userDetails['lastName']);
        $details->setNickName($userDetails['nickName']);
        $details->setContactEmail($userDetails['contactEmail']);
        $details->setPhoneNumber($userDetails['contactPhone']);
        $details->setuserId($userDetails['userId']);

        try {
            $this->entityManager()->persist($details);
            $this->entityManager()->flush();
        } catch (\Exception $ex) {

            Oforge()->Logger()->get()->addError('Could not persist / flush userDetails', ['msg' => $ex->getMessage()]);
            return false;
        }
        return true;
    }

    public function get($userId) {
        $details = $this->repository()->findBy(['userId' => $userId]);
        if (!$details) {
            return null;
        } else {
            $details = $details[0];
        }
        return $details;
    }
}