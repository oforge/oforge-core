<?php

namespace Insertion\Middleware;

use Doctrine\ORM\ORMException;
use Insertion\Models\InsertionProfile;
use Insertion\Services\InsertionProfileService;
use Oforge\Engine\Modules\Core\Exceptions\ServiceNotFoundException;
use Oforge\Engine\Modules\Core\Helper\ArrayHelper;
use Oforge\Engine\Modules\Core\Manager\Events\Event;
use Slim\Http\Request;
use Slim\Http\Response;

/**
 * Class InsertionProfileProgressMiddleware
 *
 * @package Insertion\Middleware
 */
class InsertionProfileProgressMiddleware {
    /** @var array<string, int> insertion profile data points to check */
    private $profileDataKeys = [
        'background'     => 1,
//        'description'    => 1,
        'imprintName'    => 1,
        'imprintZipCity' => 1,
        'imprintEmail'   => 1,
    ];

    /**
     * @param Request $request
     * @param Response $response
     *
     * @throws ORMException
     * @throws ServiceNotFoundException
     */
    public function prepend(Request $request, Response $response) {
        $user = Oforge()->View()->get('current_user');
        if ($user === null) {
            return;
        }
        $progress       = 0;
        $progressWeight = 0;
        $totalWeight    = 0;
        /** @var InsertionProfileService $insertionProfileService */
        $insertionProfileService = Oforge()->Services()->get('insertion.profile');
        /** @var InsertionProfile|null $userProfile */
        $userProfile = $insertionProfileService->get($user['id']);
        if ($userProfile !== null) {
            $dataKeys = Oforge()->Events()->trigger(Event::create('insertion.middleware.profileProgress.dataKeys',#
                [],# event data
                $this->profileDataKeys# return value
            ));

            $userProfileData = $userProfile->toArray(0);
            foreach ($dataKeys as $dataKey => $dataKeyWeighting) {
                $tmpValue = ArrayHelper::dotGet($userProfileData, $dataKey, null);
                $isset    = Oforge()->Events()->trigger(Event::create('insertion.middleware.profileProgress.isset',#
                    [
                        'dataKey'          => $dataKey,
                        'dataKeyWeighting' => $dataKeyWeighting,
                        'value'            => $tmpValue,
                        'user'             => $user,
                        'userProfileData'  => $userProfileData,
                    ],# event data
                    !empty($tmpValue),# return value,
                    true# stoppable
                ));
                if ($isset) {
                    $progressWeight += $dataKeyWeighting;
                }
                $totalWeight += $dataKeyWeighting;
            }
            $other = Oforge()->Events()->trigger(Event::create('insertion.middleware.profileProgress.checkOther',#
                ['user' => $user, 'userProfileData' => $userProfileData,],# event data
                [
                    'progressWeight' => 0,
                    'totalWeight'    => 0,
                ]# return value
            ));
            if (isset($other['progressWeight']) && isset($other['totalWeight'])
                && $other['progressWeight'] > 0
                && $other['totalWeight'] > 0) {
                $progressWeight += $other['progressWeight'];
                $totalWeight    += $other['totalWeight'];
            }
            if ($totalWeight === 0) {
                $totalWeight = 1;
            }
            if ($progressWeight < 0) {
                $progressWeight = 0;
            }
            if ($progressWeight > $totalWeight) {
                $progressWeight = $totalWeight;
            }
            $progress = 100 * $progressWeight / $totalWeight;
        }
        Oforge()->View()->assign(['insertionProfileProgress' => $progress]);
    }

    private function assignProgressToView(int $progress = 0) {
        Oforge()->View()->assign(['insertionProfileProgress' => $progress]);
    }

}
