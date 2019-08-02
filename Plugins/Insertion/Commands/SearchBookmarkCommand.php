<?php

namespace Insertion\Commands;

use FrontendUserManagement\Services\UserService;
use GetOpt\GetOpt;
use GetOpt\Option;
use Insertion\Models\InsertionUserSearchBookmark;
use Insertion\Services\InsertionListService;
use Insertion\Services\InsertionSearchBookmarkService;
use Monolog\Logger;
use Oforge\Engine\Modules\Console\Abstracts\AbstractCommand;
use Oforge\Engine\Modules\Console\Lib\Input;

class SearchBookmarkCommand extends AbstractCommand {

    /**
     * ReminderCommand constructor.
     */
    public function __construct() {
        parent::__construct('oforge:plugin:insertion:searchBookmark', self::TYPE_DEFAULT);
        $this->setDescription('Search bookmark mail distributor');
    }

    /**
     * Command handle function.
     *
     * @param Input $input
     * @param Logger $output
     */
    public function handle(Input $input, Logger $output) : void {
        /** @var InsertionSearchBookmarkService $searchBookmarkService */ /** @var UserService $userService */
        /** @var InsertionListService $insertionListService */
        $searchBookmarkService = Oforge()->Services()->get('insertion.search.bookmark');
        $insertionListService  = Oforge()->Services()->get('insertion.list');
        $userService           = Oforge()->Services()->get('frontend.user.management.user');
        $mailService           = Oforge()->Services()->get('mail');

        /** @var InsertionUserSearchBookmark[] $bookmarks */
        $bookmarks = $searchBookmarkService->list();
        foreach ($bookmarks as $bookmark) {
            /** @var User $user */
            $user                 = $bookmark->getUser();
            $params               = $bookmark->getParams();
            $params['after_date'] = $bookmark->getLastChecked();

            $insertionList      = $insertionListService->search($bookmark->getInsertionType()->getId(), $params);
            $newInsertionsCount = sizeof($insertionList);

            // TODO: BASTI send mail

            $searchBookmarkService->setLastChecked($bookmark);
        }
    }
}
