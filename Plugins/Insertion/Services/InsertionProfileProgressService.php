<?php

namespace Insertion\Services;

use Oforge\Engine\Modules\Core\Exceptions\ServiceNotFoundException;
use \Doctrine\ORM\ORMException;

class InsertionProfileProgressService {

    /**
     * Checks for each value in $keys if the data entry for the user is set.
     * Returns the percentage of progress
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
        $userProfile             = $insertionProfileService->get($userId)->toArray(0);

        foreach ($keys as $key) {
            if(!empty($userProfile[$key])) {
                $count++;
            }
        }
        o_print([$keys, $userProfile, $count]);

        return 100 * $count / sizeof($keys);
    }
}
