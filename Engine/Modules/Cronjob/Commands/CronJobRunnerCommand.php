<?php

namespace Oforge\Engine\Modules\Cronjob\Commands;

use Exception;
use Monolog\Logger;
use Oforge\Engine\Modules\Console\Abstracts\AbstractCommand;
use Oforge\Engine\Modules\Console\Lib\Input;
use Oforge\Engine\Modules\Cronjob\Services\CronjobService;

class CronJobRunnerCommand extends AbstractCommand {

    /**
     * ReminderCommand constructor.
     */
    public function __construct() {
        parent::__construct('cronjob:runAll', self::ALL_TYPES);
        $this->setDescription('Run all cronjobs');
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
        /**
         * @var $service CronjobService
         */
        $service = Oforge()->Services()->get('cronjob');

        $service->runAll();
    }
}
