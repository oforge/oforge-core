<?php

namespace Insertion\Commands;

use FrontendUserManagement\Models\User;
use Insertion\Models\InsertionUserSearchBookmark;
use Insertion\Services\InsertionListService;
use Insertion\Services\InsertionSearchBookmarkService;
use Monolog\Logger;
use Oforge\Engine\Modules\Console\Abstracts\AbstractCommand;
use Oforge\Engine\Modules\Console\Lib\Input;
use Oforge\Engine\Modules\Core\Helper\RouteHelper;
use Oforge\Engine\Modules\Core\Services\ConfigService;
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
     * Iterate over all saved searchBookmarks and collect those which have 'new' (depending on user's last checked datetime) search results.
     * Generate link to search results and send notification mail.
     *
     * @param Input $input
     * @param Logger $output
     *
     * @throws Exceptions\ConfigElementNotFoundException
     * @throws Exceptions\ConfigOptionKeyNotExistException
     * @throws Exceptions\ServiceNotFoundException
     * @throws \Doctrine\DBAL\DBALException
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     * @throws \Twig_Error_Loader
     * @throws \Twig_Error_Runtime
     * @throws \Twig_Error_Syntax
     */
    public function handle(Input $input, Logger $output) : void {
        /** @var InsertionSearchBookmarkService $searchBookmarkService */
        $searchBookmarkService = Oforge()->Services()->get('insertion.search.bookmark');

        /** @var InsertionListService $insertionListService */
        $insertionListService  = Oforge()->Services()->get('insertion.list');

        /** @var MailService $mailService */
        $mailService           = Oforge()->Services()->get('mail');

        /** @var ConfigService $configService */
        $configService = Oforge()->Services()->get('config');

        /** @var InsertionUserSearchBookmark[] $bookmarks */
        $bookmarks = $searchBookmarkService->list();

        foreach ($bookmarks as $bookmark) {
            /** @var User $user */
            $user                 = $bookmark->getUser();

            $params               = $bookmark->getParams();
            $insertionList        = $insertionListService->search($bookmark->getInsertionType()->getId(), $params);

            $baseUrl    = $configService->get('system_project_domain_name');
            $searchLink = $baseUrl . $searchBookmarkService->getUrl($bookmark->getInsertionType()->getId(), $params);

            $newInsertionsCount = sizeof($insertionList["query"]["items"]);
            if ($newInsertionsCount > 0) {
                $mailService->sendNewSearchResultsInfoMail($user->getId(), $newInsertionsCount, $searchLink);
            }

            $searchBookmarkService->setLastChecked($bookmark);
        }
    }
}
