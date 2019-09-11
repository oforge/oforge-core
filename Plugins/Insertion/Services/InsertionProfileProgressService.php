<?php

namespace Insertion\Services;

use Oforge\Engine\Modules\Core\Exceptions\ServiceNotFoundException;
use \Doctrine\ORM\ORMException;

class InsertionProfileProgressService {

    /**
     * Checks for each value in $keys if the data entry for the user is set.
     * Returns the percentage of set data entries
     *
     * @param $userId
     * @param array $keys
     *
     * @return int
     * @throws ORMException
     * @throws ServiceNotFoundException
     */
    public function calculateProgress($userId, $keys) {
        $count = 0;

        /** @var InsertionProfileService $insertionProfileService */
        $insertionProfileService = Oforge()->Services()->get('insertion.profile');

        $userProfile             = $insertionProfileService->get($userId);

        if(is_null($userProfile)) {
            return 0;
        }

        $userProfile = $userProfile->toArray(0);

        foreach ($keys as $key) {
            if(!empty($userProfile[$key])) {
                $count++;
            }
        }

        return 100 * $count / sizeof($keys);
    }
}
