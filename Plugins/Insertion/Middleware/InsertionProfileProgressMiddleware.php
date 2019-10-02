<?php

namespace Insertion\Middleware;

use Insertion\Services\InsertionProfileService;
use Slim\Http\Request;
use Slim\Http\Response;

/**
 * Class InsertionProfileProgressMiddleware
 *
 * @package Insertion\Middleware
 */
class InsertionProfileProgressMiddleware {

    /**
     * @param Request $request
     * @param Response $response
     *
     * @throws \Doctrine\ORM\ORMException
     * @throws \Oforge\Engine\Modules\Core\Exceptions\ServiceNotFoundException
     */
    public function prepend(Request $request, Response $response) {
        /** @var array $keys - insertion profile entries to check */
        $keys = [
            'background',
            'description',
            'imprintName',
            'imprintZipCity',
            'imprintEmail',
        ];

        $user = Oforge()->View()->get('current_user');

        /** @var int $count - represents the profile entries from $keys which have been set by the user */
        $count = 0;

        /** @var InsertionProfileService $insertionProfileService */
        $insertionProfileService = Oforge()->Services()->get('insertion.profile');

        $userProfile = $insertionProfileService->get($user['id']);

        if (is_null($userProfile)) {
            $this->assignProgressToView(0);
        } else {
            $userProfile = $userProfile->toArray(0);

            foreach ($keys as $key) {
                if (!empty($userProfile[$key])) {
                    $count++;
                }
            }
            /** @var int $progress - represents completion in percent */
            $progress = 100 * $count / sizeof($keys);

            if ($progress < 100) {
                $this->assignProgressToView(100 * $count / sizeof($keys));
            }
        }
    }

    private function assignProgressToView(int $progress = 0) {
        Oforge()->View()->assign(['insertionProfileProgress' => $progress]);
    }
}
