<?php

namespace Oforge\Engine\Modules\Console\Abstracts;

use Monolog\Logger;
use Oforge\Engine\Modules\Console\Lib\Input;
use Oforge\Engine\Modules\Console\Services\ConsoleService;
use Oforge\Engine\Modules\Core\Exceptions\ServiceNotFoundException;

/**
 * Class AbstractGroupCommand.
 * A group command calls other commands with arguments.
 *
 * @package Oforge\Engine\Modules\Console\Abstracts
 */
abstract class AbstractGroupCommand extends AbstractCommand {
    /**
     * @var ConsoleService $consoleService ;
     */
    private $consoleService;
    /**
     * @var array $groupCommands
     */
    private $groupCommands;

    /**
     * AbstractGroupCommand constructor.
     *
     * @param string $name Name of command
     * @param array $croupCommands Array with command => commandArgs pairs.
     * @param int $type [Default: AbstractCommand::TYPE_DEFAULT]
     *
     * @throws ServiceNotFoundException
     */
    public function __construct(string $name, array $croupCommands, int $type = self::TYPE_DEFAULT) {
        parent::__construct($name, $type);
        $this->groupCommands = $croupCommands;
        try {
            $this->consoleService = Oforge()->Services()->get('console');
        } catch (ServiceNotFoundException $exception) {
            Oforge()->Logger()->get()->error($exception->getMessage(), $exception->getTrace());
            throw $exception;
        }
    }

    /**
     * @inheritdoc
     */
    public function handle(Input $input, Logger $output) : void {
        $commands = $this->groupCommands;
        foreach ($commands as $command => $commandArgs) {
            if (!is_string($command)) {
                $command     = $commandArgs;
                $commandArgs = '';
            }
            if ($this->getName() === $command) {
                continue;
            }
            $commandArgs = is_string($commandArgs) && !empty($commandArgs) ? $commandArgs : '';
            $output->notice('Run group sub command: ' . $command . ' ' . $commandArgs);
            $this->consoleService->runCommand($command, $commandArgs);
        }
    }

}
