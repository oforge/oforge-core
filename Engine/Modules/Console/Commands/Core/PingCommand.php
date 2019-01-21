<?php

namespace Oforge\Engine\Modules\Console\Commands\Core;

use Monolog\Logger;
use Oforge\Engine\Modules\Console\Abstracts\AbstractCommand;
use Oforge\Engine\Modules\Console\Lib\Input;
use Oforge\Engine\Modules\Core\Exceptions\ServiceNotFoundException;
use Oforge\Engine\Modules\Core\Services\PingService;

/**
 * Class PingCommand
 *
 * @package Oforge\Engine\Modules\Console\Commands\Core
 */
class PingCommand extends AbstractCommand {

    /**
     * PingCommand constructor.
     */
    public function __construct() {
        parent::__construct('ping', self::TYPE_DEFAULT);
        $this->setDescription('Ping Oforge');
    }

    /**
     * @inheritdoc
     * @throws ServiceNotFoundException
     */
    public function handle(Input $input, Logger $output) : void {
        try {
            /** @var PingService $pingService */
            $pingService = Oforge()->Services()->get('ping');
            $output->notice($pingService->me());
        } catch (ServiceNotFoundException $exception) {
            Oforge()->Logger()->get()->error($exception->getMessage(), $exception->getTrace());
            throw $exception;
        }
    }

}
