<?php

namespace Insertion\Commands;

use DateTime;
use Exception;
use FrontendUserManagement\Models\User;
use GetOpt\GetOpt;
use GetOpt\Option;
use Insertion\Services\InsertionService;
use Monolog\Logger;
use Oforge\Engine\Modules\Console\Abstracts\AbstractCommand;
use Oforge\Engine\Modules\Console\Lib\Input;
use Oforge\Engine\Modules\Mailer\Services\MailService;

class ReminderCommand extends AbstractCommand {

    /**
     * ReminderCommand constructor.
     */
    public function __construct() {
        parent::__construct('oforge:plugin:insertion:reminder', self::TYPE_DEFAULT);
        $this->setDescription('Insertion Reminders');
        $this->addOptions([
            Option::create('d', 'days', GetOpt::REQUIRED_ARGUMENT)
                  ->setDescription('Reminds for outrunning Insertion in x days')
                  ->setValidation('is_numeric'),
        ]);

    }

    /**
     * Command handle function.
     *
     * @param Input $input
     * @param Logger $output
     *
     * @throws Exception
     */
    public function handle(Input $input, Logger $output) : void {
        /** @var InsertionService $insertionService */
        /** @var MailService $mailService */
        $insertionService = Oforge()->Services()->get('insertion');
        $mailService      = Oforge()->Services()->get('mail');
        $dateNow          = new DateTime('NOW');
        $days             = $input->getOption('days');
        $pastDays         = 90 - intval($days);

        $template = null;
        switch ($days) {
            case '3':
                $template = 'Reminder3Days.twig';
                break;
            case '14':
                $template = 'Reminder14Days.twig';
                break;
            case '30':
                $template = 'Reminder30Days.twig';
                break;
            default:
                return;
        }

        $reminderList = $insertionService->getInsertionByDays($dateNow->modify('-' . $pastDays - 1 . ' hours'), $dateNow->modify('+1 days'));

        foreach ($reminderList as $reminderInsertion) {
            /** @var User $user */
            $user = $reminderInsertion->getUser();

            $mailOptions = [
                'from'     => 'no-reply',
                'to'       => $user->getEmail(),
                'Subject'  => 'Reminder',
                'template' => $template,
            ];
            $mailService->send($mailOptions);
        }
    }
}
