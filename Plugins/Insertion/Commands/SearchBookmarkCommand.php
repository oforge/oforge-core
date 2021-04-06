<?php

namespace Insertion\Commands;

use DateTime;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use Exception;
use FrontendUserManagement\Models\User;
use Insertion\Helper\InsertionMail;
use Insertion\Models\InsertionUserSearchBookmark;
use Insertion\Services\InsertionListService;
use Insertion\Services\InsertionSearchBookmarkService;
use Monolog\Logger;
use Oforge\Engine\Modules\Console\Abstracts\AbstractCommand;
use Oforge\Engine\Modules\Console\Lib\Input;
use Oforge\Engine\Modules\Core\Helper\RouteHelper;
use Oforge\Engine\Modules\Core\Services\ConfigService;
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

        /** @var ConfigService $configService */
        $configService = Oforge()->Services()->get('config');

        /** @var InsertionUserSearchBookmark[] $bookmarks */
        $bookmarks = $searchBookmarkService->list();

        foreach ($bookmarks as $bookmark) {
            if (!$this->inInterval($bookmark->getCheckInterval(), $bookmark->getLastChecked())) continue;

            /** @var User $user */
            $user                 = $bookmark->getUser();

            $params               = $bookmark->getParams();
            $params['after_date'] = $bookmark->getLastChecked();
            $insertionList        = $insertionListService->search($bookmark->getInsertionType()->getId(), $params);

            $baseUrl    = $configService->get('system_project_domain_name');
            $searchLink = $baseUrl . $searchBookmarkService->getUrl($bookmark->getInsertionType()->getId(), $params);

            $newInsertionsCount = count($insertionList["query"]["items"]);

            try {
                if ($newInsertionsCount > 0) {
                    InsertionMail::sendNewSearchResultsMail($user->getId(), $newInsertionsCount, $searchLink);
                }
                $searchBookmarkService->setLastChecked($bookmark);
            } catch (Exception $exception) {
                Oforge()->Logger()->logException($exception);
            }
        }
    }

    private function inInterval($interval, \DateTime $lastChecked) {
        switch ($interval) {
            case InsertionSearchBookmarkService::DAILY:
                $lastChecked->add(new \DateInterval(('P1D')));
                break;
            case InsertionSearchBookmarkService::DAYS_2:
                $lastChecked->add(new \DateInterval(('P2D')));
                break;
            case InsertionSearchBookmarkService::DAYS_3:
                $lastChecked->add(new \DateInterval(('P3D')));
                break;
            case InsertionSearchBookmarkService::WEEKLY:
                $lastChecked->add(new \DateInterval(('P7D')));
                break;
            case InsertionSearchBookmarkService::NONE:
            default:
                return false;
        }

        if (new DateTime() > $lastChecked) {
            return true;
        }
        return false;
    }
}
