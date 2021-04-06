<?php

namespace Insertion\Commands;

use FrontendUserManagement\Models\User;
use FrontendUserManagement\Services\FrontendUserService;
use FrontendUserManagement\Services\UserService;
use Insertion\Services\InsertionBookmarkService;
use Insertion\Services\InsertionListService;
use Monolog\Logger;
use Oforge\Engine\Modules\Console\Abstracts\AbstractCommand;
use Oforge\Engine\Modules\Console\Lib\Input;
use Oforge\Engine\Modules\I18n\Helper\I18N;
use Oforge\Engine\Modules\Mailer\Services\MailService;

class InsertionBookmarkReminderCommand extends AbstractCommand {

    /**
     * ReminderCommand constructor.
     */
    public function __construct() {
        parent::__construct('oforge:plugin:insertion:insertionBookmarkReminder', self::TYPE_DEFAULT);
        $this->setDescription('Search bookmark mail distributor');
    }

    public function handle(Input $input, Logger $output) : void {

        /** @var InsertionBookmarkService $bookmarkService */
        $bookmarkService = Oforge()->Services()->get('insertion.bookmark');
        /** @var MailService $mailService */
        $mailService           = Oforge()->Services()->get('mail');
        /** @var UserService $userService */
        $userService = Oforge()->Services()->get('frontend.user.management.user');

        $userList = $bookmarkService->getUsersWithBookmarks();

        foreach ($userList as $userId) {
            $user        = $userService->getUserById($userId);
            $userMail = $user->getEmail();

            $bookmarks = $bookmarkService->list($user);
            shuffle($bookmarks);

            $tmp = [];
            foreach ($bookmarks as $bookmark) {
                if(sizeof($tmp) > 3) {
                    break;
                }

                if($bookmark['insertion']['active'] && !$bookmark['insertion']['deleted'] && $bookmark['insertion']['moderation']) {
                    $tmp[] = $bookmark;
                }
            }
            $bookmarks = $tmp;

            if(sizeof($bookmarks) < 1) {
                continue;
            }

            $mailConfig = [
                'to'       => [$userMail => $userMail],
                'from'     => $mailService->buildFromConfigByPrefix('no_reply'),
                'subject'  => I18N::translate('mailer_subject_insertion_bookmark_reminder', 'Check mal wieder deine Merkliste | All Your Horses'),
                'template' => 'InsertionBookmarkReminder.twig',
            ];

            $templateData  = [
                'sender_mail'   => 'no_reply',
                'receiver_name' => $user->getDetail()->getNickName(),
                'bookmarks' => $bookmarks,
                'baseUrl' => Oforge()->Settings()->get('host_url'),
            ];

            $mailService->send($mailConfig, $templateData);
        }
    }
}
