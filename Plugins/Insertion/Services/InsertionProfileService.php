<?php

namespace Insertion\Services;

use Insertion\Models\InsertionProfile;
use Oforge\Engine\Modules\Core\Abstracts\AbstractDatabaseAccess;

class InsertionProfileService extends AbstractDatabaseAccess {
    public function __construct() {
        parent::__construct([
            'default' => InsertionProfile::class,
        ]);
    }

    /**
     * @param int $user
     *
     * @return InsertionProfile|null
     * @throws \Doctrine\ORM\ORMException
     */
    public function get(int $user) : ?InsertionProfile {
        /**
         * @var $result InsertionProfile
         */
        $result = $this->repository()->findOneBy(["user" => $user]);

        return $result;
    }

    public function update(int $user, array $params) {
        /**
         * @var $result InsertionProfile
         */
        $result = $this->repository()->findOneBy(["user" => $user]);

        if($result != null) {

        } else {

        }
    }

}
