<?php

namespace Insertion\Commands;

use FrontendUserManagement\Models\User;
use Insertion\Models\InsertionUserSearchBookmark;
use Insertion\Services\InsertionListService;
use Insertion\Services\InsertionSearchBookmarkService;
use Monolog\Logger;
use Oforge\Engine\Modules\Console\Abstracts\AbstractCommand;
use Oforge\Engine\Modules\Console\Lib\Input;
use Oforge\Engine\Modules\Mailer\Services\MailService;
use Oforge\Engine\Modules\Core\Exceptions;

class SearchBookmarkCommand extends AbstractCommand {

    /**
     * ReminderCommand constructor.
     */
    public function __construct() {
        parent::__construct('oforge:plugin:insertion:searchBookmark', self::TYPE_DEFAULT);
        $this->setDescription('Search bookmark mail distributor');
    }

    /**
     * Command handle function
     *
     * @param Input $input
     * @param Logger $output
     *
     * @throws \Doctrine\ORM\ORMException
     * @throws Exceptions\ConfigElementNotFoundException
     * @throws Exceptions\ConfigOptionKeyNotExistException
     * @throws Exceptions\ServiceNotFoundException
     * @throws \Twig_Error_Loader
     * @throws \Twig_Error_Runtime
     * @throws \Twig_Error_Syntax
     */
    public function handle(Input $input, Logger $output) : void {
        /** @var InsertionSearchBookmarkService $searchBookmarkService */
        /** @var InsertionListService $insertionListService */
        $searchBookmarkService = Oforge()->Services()->get('insertion.search.bookmark');
        $insertionListService  = Oforge()->Services()->get('insertion.list');

        /** @var MailService $mailService */
        $mailService           = Oforge()->Services()->get('mail');

        /** @var InsertionUserSearchBookmark[] $bookmarks */
        $bookmarks = $searchBookmarkService->list();
        foreach ($bookmarks as $bookmark) {
            /** @var User $user */
            $user                 = $bookmark->getUser();
            $params               = $bookmark->getParams();
            $params['after_date'] = $bookmark->getLastChecked();

            $insertionList      = $insertionListService->search($bookmark->getInsertionType()->getId(), $params);

            $newInsertionsCount = sizeof($insertionList["query"]["items"]);
            if ($newInsertionsCount > 0) {
                $searchLink = $searchBookmarkService->getUrl($bookmark->getInsertionType(), $params);
                $mailService->sendNewSearchResultsInfoMail($user->getId(), $newInsertionsCount, $searchLink);
            }

            $searchBookmarkService->setLastChecked($bookmark);
        }
    }
}
