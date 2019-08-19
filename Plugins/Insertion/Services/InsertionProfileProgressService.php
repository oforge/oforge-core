<?php

namespace Insertion\Services;


class InsertionProfileProgressService  {
    /**
     * @param $userId
     *
     * @return int
     * @throws \Doctrine\ORM\ORMException
     * @throws \Oforge\Engine\Modules\Core\Exceptions\ServiceNotFoundException
     */
    public function calculateProgress($userId) {
        /** @var InsertionProfileService $insertionProfileService */
        $insertionProfileService = Oforge()->Services()->get('insertion.profile');
        $userProfile = $insertionProfileService->get($userId)->toArray(4);
        $keys = [
            'background',
            'description',
            'imprintName',
            'imprintStreet',
            'imprintZipCity',
            'imprintPhone',
            'imprintEMail',
            ];
        $count = 0;
        return $userProfile;
    }
}