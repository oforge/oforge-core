<?php

namespace Oforge\Engine\Modules\Cronjob\Commands;

use Exception;
use GetOpt\GetOpt;
use GetOpt\Option;
use Monolog\Logger;
use Oforge\Engine\Modules\Console\Abstracts\AbstractCommand;
use Oforge\Engine\Modules\Console\Lib\Input;
use Oforge\Engine\Modules\Cronjob\Services\CronjobService;

class CronJobForcedRunnerCommand extends AbstractCommand {

    /**
     * ReminderCommand constructor.
     */
    public function __construct() {
        parent::__construct('cronjob:run', self::ALL_TYPES);
        $this->addOptions([
            Option::create('n', 'name', GetOpt::REQUIRED_ARGUMENT)
                  ->setDescription('Name of cronjob')
                  ->setValidation('is_string'),
        ]);
        $this->setDescription('Run a specific cronjob');
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
        $name             = $input->getOption('name');
        $service->run($name);
    }
}
