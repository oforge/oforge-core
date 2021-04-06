<?php

namespace Oforge\Engine\Modules\Cronjob\Commands;

use Exception;
use Monolog\Logger;
use Oforge\Engine\Modules\Console\Abstracts\AbstractCommand;
use Oforge\Engine\Modules\Console\Lib\Input;
use Oforge\Engine\Modules\Core\Services\ConfigService;
use Oforge\Engine\Modules\Mailer\Services\MailService;

class PingCommand extends AbstractCommand {

    /**
     * ReminderCommand constructor.
     */
    public function __construct() {
        parent::__construct('oforge:cronjob:ping', self::ALL_TYPES);
        $this->setDescription('Ping to email');
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
        /** @var MailService $mailService */
        $mailService = Oforge()->Services()->get('mail');

        /** @var ConfigService $configService */
        $configService = Oforge()->Services()->get('config');

        $email = $configService->get("cronjob_ping_email");

        $emailText = $configService->get("cronjob_ping_text");

        if (!empty($email) && !empty($emailText)) {
            $mailConfig = [
                'from'    => $mailService->buildFromConfigByPrefix('no_reply'),
                'to'      => [$email => $email],
                'subject' => 'Ping ' . Oforge()->Settings()->get('host_url') ,
                'html'    => $emailText,
            ];
            $mailService->send($mailConfig);
        }

    }
}
