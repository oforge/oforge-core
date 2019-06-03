<?php

namespace FrontendUserManagement\Services;

use Doctrine\ORM\ORMException;
use Exception;
use FrontendUserManagement\Models\UserDetail;
use Oforge\Engine\Modules\Core\Abstracts\AbstractDatabaseAccess;

/**
 * Class UserDetailsService
 *
 * @package FrontendUserManagement\Services
 */
class UserDetailsService extends AbstractDatabaseAccess {

    public function __construct() {
        parent::__construct(UserDetail::class);
    }

    /**
     * @param array $data
     *
     * @return bool
     */
    public function save(array $data) {
        try {
            $detail = $this->get($data['userId']);
            if (isset($detail)) {
                $detail->fromArray($data);
                $this->entityManager()->update($detail);

                return true;
            }
        } catch (Exception $ex) {
            Oforge()->Logger()->get()->addError('Could not persist / flush userDetails', ['msg' => $ex->getMessage()]);
        }

        return false;
    }

    /**
     * @param $userID
     *
     * @return UserDetail|null
     * @throws ORMException
     */
    public function get($userID) : ?UserDetail {
        /** @var UserDetail|null $detail */
        $detail = $this->repository()->findOneBy(['user' => $userID]);

        return $detail;
    }

}
