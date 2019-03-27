<?php

namespace Oforge\Engine\Modules\Console\Commands\Cleanup;

use GetOpt\GetOpt;
use GetOpt\Option;
use Monolog\Logger;
use Oforge\Engine\Modules\Console\Abstracts\AbstractCommand;
use Oforge\Engine\Modules\Console\Lib\Input;

/**
 * Class CleanupLogfilesCommand
 *
 * @package Oforge\Engine\Modules\Console\Commands\Cleanup
 */
class CleanupLogfilesCommand extends AbstractCommand {

    /**
     * CleanupLogfilesCommand constructor.
     */
    public function __construct() {
        parent::__construct('oforge:cleanup:logs', self::TYPE_DEFAULT);
        $this->setDescription('Cleanup log files.');
        $this->addOptions([
            Option::create('d', 'days', GetOpt::OPTIONAL_ARGUMENT)#
                  ->setDescription('Remove files older x days')#
                  ->setValidation('is_numeric')->setDefaultValue(false),#
        ]);
    }

    /**
     * @inheritdoc
     */
    public function handle(Input $input, Logger $output) : void {
        if ($input->getOption('days')) {
            Oforge()->Logger()->cleanupLogfiles((int) $input->getOption('days'));
        } else {
            Oforge()->Logger()->cleanupLogfiles();
        }
    }

}
